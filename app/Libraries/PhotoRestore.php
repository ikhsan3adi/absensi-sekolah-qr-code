<?php

namespace App\Libraries;

use RuntimeException;
use ZipArchive;

/**
 * Restores photo archives without trusting ZIP entry names or file types.
 */
class PhotoRestore
{
    private const ALLOWED_PHOTO_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    private const SKIPPED_FILES = ['index.html', 'qrcode-siswa.zip'];

    private const SKIPPED_TMP_EXTENSIONS = ['csv', 'txt'];

    private string $uploadsPath;

    private string $realUploadsPath;

    public function __construct(string $uploadsPath)
    {
        $realUploadsPath = realpath($uploadsPath);

        if ($realUploadsPath === false || ! is_dir($realUploadsPath)) {
            throw new RuntimeException('Folder uploads tidak ditemukan.');
        }

        $this->uploadsPath     = rtrim($uploadsPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->realUploadsPath = rtrim($realUploadsPath, DIRECTORY_SEPARATOR);
    }

    /**
     * @return array{
     *     deleted: list<string>,
     *     extracted: list<string>,
     *     failed_delete: list<string>,
     *     failed_extract: list<string>
     * }
     */
    public function restore(string $zipPath): array
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('File ZIP tidak dapat dibuka.');
        }

        try {
            $entries = $this->collectAllowedEntries($zip);

            if ($entries === []) {
                throw new RuntimeException('Tidak ada file foto yang dapat direstore.');
            }

            $deleted      = [];
            $failedDelete = [];

            foreach ($entries as $entry) {
                $targetPath = $this->uploadsPath . $entry;

                if (is_link($targetPath) || ! is_file($targetPath)) {
                    continue;
                }

                if (@unlink($targetPath)) {
                    $deleted[] = $entry;
                } else {
                    $failedDelete[] = $entry;
                }
            }

            [$extracted, $failedExtract] = $this->extractEntries($zip, $entries);
        } finally {
            $zip->close();
        }

        return [
            'deleted'        => $deleted,
            'extracted'      => $extracted,
            'failed_delete'  => $failedDelete,
            'failed_extract' => $failedExtract,
        ];
    }

    /**
     * @return list<string>
     */
    private function collectAllowedEntries(ZipArchive $zip): array
    {
        $entries = [];

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);

            if (! is_string($name) || $name === '') {
                throw new RuntimeException('Nama file di dalam ZIP tidak valid.');
            }

            $this->assertSafeEntryName($name);

            if (str_ends_with($name, '/')) {
                continue;
            }

            if ($this->isSymlinkEntry($zip, $index)) {
                throw new RuntimeException('Symlink tidak diizinkan di dalam ZIP.');
            }

            $targetPath = $this->uploadsPath . $name;

            if (is_link($targetPath)) {
                throw new RuntimeException('Target restore tidak boleh berupa symlink.');
            }

            $this->assertParentIsInsideUploads($targetPath);

            if ($this->isAllowedPhoto($name)) {
                $entries[] = $name;

                continue;
            }

            if ($this->isSkippableFile($name)) {
                continue;
            }

            throw new RuntimeException('Tipe file di dalam ZIP tidak diizinkan.');
        }

        return $entries;
    }

    private function assertSafeEntryName(string $name): void
    {
        if (str_contains($name, "\0") || str_contains($name, '\\')) {
            throw new RuntimeException('Nama file di dalam ZIP tidak valid.');
        }

        if (str_starts_with($name, '/') || preg_match('/^[A-Za-z]:/', $name) === 1) {
            throw new RuntimeException('Path absolut tidak diizinkan.');
        }

        $segments = explode('/', rtrim($name, '/'));

        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                throw new RuntimeException('Path traversal tidak diizinkan.');
            }
        }
    }

    private function assertParentIsInsideUploads(string $targetPath): void
    {
        $parentPath = dirname($targetPath);

        while ($parentPath !== $this->realUploadsPath && ! is_dir($parentPath)) {
            $parentPath = dirname($parentPath);
        }

        $realParentPath = realpath($parentPath);

        if ($realParentPath === false || ! $this->isInsideUploads($realParentPath)) {
            throw new RuntimeException('Path file berada di luar folder uploads.');
        }
    }

    private function isInsideUploads(string $path): bool
    {
        $path = rtrim($path, DIRECTORY_SEPARATOR);

        return $path === $this->realUploadsPath
            || str_starts_with($path, $this->realUploadsPath . DIRECTORY_SEPARATOR);
    }

    private function isAllowedPhoto(string $name): bool
    {
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (in_array($extension, self::ALLOWED_PHOTO_EXTENSIONS, true)) {
            return true;
        }

        // SVG is only used for the site logo. Do not allow it in photo folders.
        return $extension === 'svg' && str_starts_with($name, 'logo/');
    }

    private function isSkippableFile(string $name): bool
    {
        if (in_array(basename($name), self::SKIPPED_FILES, true)) {
            return true;
        }

        return str_starts_with($name, 'tmp/')
            && in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), self::SKIPPED_TMP_EXTENSIONS, true);
    }

    private function isSymlinkEntry(ZipArchive $zip, int $index): bool
    {
        $stat = $zip->statIndex($index);

        if (! is_array($stat)) {
            return false;
        }

        $attributes = (int) ($stat['external_attributes'] ?? 0);
        $mode       = ($attributes >> 16) & 0xFFFF;

        return ($mode & 0xF000) === 0xA000;
    }

    /**
     * @param list<string> $entries
     *
     * @return array{0: list<string>, 1: list<string>}
     */
    private function extractEntries(ZipArchive $zip, array $entries): array
    {
        $extracted = [];
        $failed    = [];

        foreach ($entries as $entry) {
            $stream = $zip->getStream($entry);

            if ($stream === false) {
                $failed[] = $entry;

                continue;
            }

            $targetPath = $this->uploadsPath . $entry;
            $targetDir  = dirname($targetPath);

            if (! is_dir($targetDir) && ! @mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
                fclose($stream);
                $failed[] = $entry;

                continue;
            }

            $output = @fopen($targetPath, 'wb');

            if ($output === false) {
                fclose($stream);
                $failed[] = $entry;

                continue;
            }

            $copied = stream_copy_to_stream($stream, $output);

            fclose($stream);
            fclose($output);

            if ($copied === false) {
                @unlink($targetPath);
                $failed[] = $entry;

                continue;
            }

            $extracted[] = $entry;
        }

        return [$extracted, $failed];
    }
}
