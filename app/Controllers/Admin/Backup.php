<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class Backup extends BaseController
{
    const UPLOADS_PATH = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;

    public function index()
    {
        $data = [
            'ctx' => 'backup',
            'title' => 'Backup & Restore',
        ];

        return view('admin/backup/index', $data);
    }

    public function dbBackup()
    {
        $db = \Config\Database::connect();
        $hostname = $db->hostname;
        $username = $db->username;
        $password = $db->password;
        $database = $db->database;
        $filename = 'backup_db_' . date('Ymd_His') . '.sql';

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $command = "mysqldump --skip-ssl --host={$hostname} --user={$username} --password={$password} {$database}";
        passthru($command);
        exit;
    }

    public function dbRestore()
    {
        $file = $this->request->getFile('file_backup_db');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', $file->getErrorString());
        }

        $extension = $file->getClientExtension();
        if ($extension !== 'sql') {
            return redirect()->back()->with('error', 'Format file harus .sql');
        }

        $db = \Config\Database::connect();
        $hostname = $db->hostname;
        $username = $db->username;
        $password = $db->password;
        $database = $db->database;

        try {
            $filePath = $file->getTempName();
            $command = "mysql --skip-ssl --host={$hostname} --user={$username} --password={$password} {$database} < {$filePath}";

            // Execute the command
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception(implode("\n", $output));
            }

            return redirect()->back()->with('success', 'Database berhasil direstore.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal merestore database: ' . $e->getMessage());
        }
    }

    public function photosBackup()
    {
        $zipFileName = 'backup_photos_' . date('Ymd_His') . '.zip';
        $zipFilePath = sys_get_temp_dir() . '/' . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(self::UPLOADS_PATH),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen(self::UPLOADS_PATH));
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        } else {
            return redirect()->back()->with('error', 'Gagal membuat file zip');
        }

        return $this->response->download($zipFilePath, null)->setFileName($zipFileName);
    }

    public function photosRestore()
    {
        $file = $this->request->getFile('file_backup_photos');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', $file->getErrorString());
        }

        $extension = $file->getClientExtension();
        if ($extension !== 'zip') {
            return redirect()->back()->with('error', 'Format file harus .zip');
        }

        $zip = new ZipArchive();
        if ($zip->open($file->getTempName()) === TRUE) {
            // Delete existing files before extracting
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $targetPath = self::UPLOADS_PATH . $filename;

                if (file_exists($targetPath) && !is_dir($targetPath)) {
                    @unlink($targetPath);
                }
            }

            $zip->extractTo(self::UPLOADS_PATH);
            $zip->close();
            return redirect()->back()->with('success', 'Foto berhasil direstore.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengekstrak file zip.');
        }
    }
}
