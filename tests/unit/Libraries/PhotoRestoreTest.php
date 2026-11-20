<?php

namespace Tests\Unit\Libraries;

use App\Libraries\PhotoRestore;
use CodeIgniter\Test\CIUnitTestCase;
use RuntimeException;
use ZipArchive;

/**
 * @internal
 */
final class PhotoRestoreTest extends CIUnitTestCase
{
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    private string $root;

    private string $uploads;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root    = sys_get_temp_dir() . '/photo-restore-' . bin2hex(random_bytes(4));
        $this->uploads = $this->root . '/uploads';

        mkdir($this->uploads . '/logo', 0755, true);
        mkdir($this->uploads . '/tmp', 0755, true);
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->root);

        parent::tearDown();
    }

    public function testTraversalEntryDoesNotDeleteOutsideFile(): void
    {
        $sentinel = $this->root . '/keep.txt';
        file_put_contents($sentinel, 'keep');

        $zip = $this->makeZip(['../keep.txt' => 'owned']);

        try {
            (new PhotoRestore($this->uploads))->restore($zip);
            $this->fail('Traversal entry should be rejected.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('traversal', strtolower($e->getMessage()));
        }

        $this->assertFileExists($sentinel);
        $this->assertSame('keep', file_get_contents($sentinel));
    }

    public function testArchiveWithUnsafeEntryIsRejectedBeforeDeletingAnything(): void
    {
        $target = $this->uploads . '/logo/test.png';
        file_put_contents($target, 'old');

        $zip = $this->makeZip([
            'logo/test.png' => 'new',
            '../keep.txt'   => 'owned',
        ]);

        try {
            (new PhotoRestore($this->uploads))->restore($zip);
            $this->fail('Archive with traversal entry should be rejected.');
        } catch (RuntimeException) {
            // Expected.
        }

        $this->assertSame('old', file_get_contents($target));
        $this->assertFileDoesNotExist($this->root . '/keep.txt');
    }

    public function testExecutableEntriesAreRejected(): void
    {
        foreach (['shell.php', '.htaccess', 'phtml.phtml'] as $entry) {
            $zip = $this->makeZip([$entry => '<?php echo 1;']);

            try {
                (new PhotoRestore($this->uploads))->restore($zip);
                $this->fail("Entry {$entry} should be rejected.");
            } catch (RuntimeException) {
                // Expected.
            }

            $this->assertFileDoesNotExist($this->uploads . '/' . $entry);
        }
    }

    public function testAbsoluteAndWindowsPathsAreRejected(): void
    {
        foreach (['/tmp/shell.png', 'C:\\shell.png', '..\\shell.png', './shell.png'] as $entry) {
            $zip = $this->makeZip([$entry => 'x']);

            try {
                (new PhotoRestore($this->uploads))->restore($zip);
                $this->fail("Entry {$entry} should be rejected.");
            } catch (RuntimeException) {
                // Expected.
            }
        }
    }

    public function testValidPhotoIsExtracted(): void
    {
        $contents = base64_decode(self::PNG, true);
        $zip      = $this->makeZip(['logo/test.png' => $contents]);

        $result = (new PhotoRestore($this->uploads))->restore($zip);

        $this->assertSame(['logo/test.png'], $result['extracted']);
        $this->assertSame([], $result['failed_extract']);
        $this->assertSame([], $result['failed_delete']);
        $this->assertFileExists($this->uploads . '/logo/test.png');
        $this->assertSame($contents, file_get_contents($this->uploads . '/logo/test.png'));
    }

    public function testExistingPhotoIsReplaced(): void
    {
        $target = $this->uploads . '/logo/test.png';
        file_put_contents($target, 'old');

        $zip    = $this->makeZip(['logo/test.png' => 'new']);
        $result = (new PhotoRestore($this->uploads))->restore($zip);

        $this->assertSame(['logo/test.png'], $result['deleted']);
        $this->assertSame(['logo/test.png'], $result['extracted']);
        $this->assertSame('new', file_get_contents($target));
    }

    public function testKnownNonPhotoFilesAreSkipped(): void
    {
        $zip = $this->makeZip([
            'index.html'    => '<html></html>',
            'tmp/data.csv'  => 'a,b',
            'logo/test.png' => base64_decode(self::PNG, true),
        ]);

        $result = (new PhotoRestore($this->uploads))->restore($zip);

        $this->assertSame(['logo/test.png'], $result['extracted']);
        $this->assertFileDoesNotExist($this->uploads . '/index.html');
        $this->assertFileDoesNotExist($this->uploads . '/tmp/data.csv');
    }

    /**
     * @param array<string, string> $entries
     */
    private function makeZip(array $entries): string
    {
        $path = $this->root . '/' . bin2hex(random_bytes(4)) . '.zip';
        $zip  = new ZipArchive();

        $this->assertTrue($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true);

        foreach ($entries as $name => $contents) {
            $zip->addFromString($name, $contents);
        }

        $zip->close();

        return $path;
    }

    private function deleteDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir() && ! $file->isLink()) {
                @rmdir($file->getPathname());
            } else {
                @unlink($file->getPathname());
            }
        }

        @rmdir($path);
    }
}
