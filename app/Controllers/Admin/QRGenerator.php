<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WriterInterface;

class QRGenerator extends BaseController
{
   protected QrCode $qrCode;
   protected WriterInterface $writer;
   protected ?Logo $logo = null;
   protected Label $label;
   protected Font $labelFont;
   protected Color $foregroundColor;
   protected Color $foregroundColor2;
   protected Color $backgroundColor;

   protected string $qrCodeFilePath;

   const UPLOADS_PATH = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;

   public function __construct()
   {
      $this->setQrCodeFilePath(self::UPLOADS_PATH);

      $this->writer = new PngWriter();

      $this->labelFont = new Font(FCPATH . 'assets/fonts/Roboto-Medium.ttf', 14);

      $this->foregroundColor = new Color(44, 73, 162);
      $this->foregroundColor2 = new Color(28, 101, 90);
      $this->backgroundColor = new Color(255, 255, 255);

      if (filter_var(env('QR_LOGO'), FILTER_VALIDATE_BOOLEAN)) {
         // Create logo
          $settings = (new \Config\School)::$generalSettings ?? null;
          $logo = ($settings ? ($settings->logo ?? false) : false);
         if (empty($logo) || !file_exists(FCPATH . $logo)) {
            $logo = 'assets/img/logo_sekolah.jpg';
         }
         if (file_exists(FCPATH . $logo)) {
            $fileExtension = pathinfo(FCPATH . $logo, PATHINFO_EXTENSION);
            if ($fileExtension === 'svg') {
               $this->writer = new SvgWriter();
               $this->logo = Logo::create(FCPATH . $logo)
                  ->setResizeToWidth(75)
                  ->setResizeToHeight(75);
            } else {
               $this->logo = Logo::create(FCPATH . $logo)
                  ->setResizeToWidth(75);
            }
         }
      }

      $this->label = Label::create('')
         ->setFont($this->labelFont)
         ->setTextColor($this->foregroundColor);

      // Create QR code
      $this->qrCode = QrCode::create('')
         ->setEncoding(new Encoding('UTF-8'))
         ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
         ->setSize(300)
         ->setMargin(10)
         ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
         ->setForegroundColor($this->foregroundColor)
         ->setBackgroundColor($this->backgroundColor);
   }

   public function setQrCodeFilePath(string $qrCodeFilePath)
   {
      $this->qrCodeFilePath = $qrCodeFilePath;
      if (!file_exists($this->qrCodeFilePath))
         mkdir($this->qrCodeFilePath, 0777, true);
   }

   public function generateQrSiswa()
   {
      $kelas = $this->getKelasJurusanSlug($this->request->getVar('id_kelas'));
      if (!$kelas) {
         return $this->response->setJSON(false);
      }

      $this->qrCodeFilePath .= "qr-siswa/$kelas/";

      if (!file_exists($this->qrCodeFilePath)) {
         mkdir($this->qrCodeFilePath, 0777, true);
      }

      try {
         $this->generate(
            unique_code: $this->request->getVar('unique_code'),
            nama: $this->request->getVar('nama'),
            nomor: $this->request->getVar('nomor')
         );
         return $this->response->setJSON(true);
      } catch (\Throwable $th) {
         log_message('error', 'QR Siswa generate failed: ' . $th->getMessage());
         return $this->response->setJSON(false);
      }
   }

   public function generateQrGuru()
   {
      $this->qrCode->setForegroundColor($this->foregroundColor2);
      $this->label->setTextColor($this->foregroundColor2);

      $this->qrCodeFilePath .= 'qr-guru/';

      if (!file_exists($this->qrCodeFilePath)) {
         mkdir($this->qrCodeFilePath, 0777, true);
      }

      try {
         $this->generate(
            unique_code: $this->request->getVar('unique_code'),
            nama: $this->request->getVar('nama'),
            nomor: $this->request->getVar('nomor')
         );
         return $this->response->setJSON(true);
      } catch (\Throwable $th) {
         log_message('error', 'QR Guru generate failed: ' . $th->getMessage());
         return $this->response->setJSON(false);
      }
   }

   public function generate($nama, $nomor, $unique_code)
   {
      $fileExt = $this->writer instanceof SvgWriter ? 'svg' : 'png';
      $filename = url_title($nama, lowercase: true) . "_" . url_title($nomor, lowercase: true) . ".$fileExt";

      // set qr code data
      $this->qrCode->setData($unique_code);

      $this->label->setText($nama);

      // Save it to a file
      $this->writer
         ->write(
            qrCode: $this->qrCode,
            logo: $this->logo,
            label: $this->label
         )
         ->saveToFile(
            path: $this->qrCodeFilePath . $filename
         );

      return $this->qrCodeFilePath . $filename;
   }

   protected function serveQr(string $filePath, bool $download)
   {
      if (!$filePath || !file_exists($filePath)) {
         throw new \RuntimeException('File QR tidak ditemukan');
      }

      if ($download) {
         return $this->response->download($filePath, null, true);
      }

      $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
      $mime = $ext === 'svg' ? 'image/svg+xml' : 'image/png';

      return $this->response
         ->setHeader('Content-Type', $mime)
         ->setHeader('Content-Length', (string) filesize($filePath))
         ->setBody(file_get_contents($filePath));
   }

   protected function generateAndServe(int|string $id, string $type, bool $download)
   {
      if ($type === 'siswa') {
         $siswa = (new SiswaModel)->find($id);
         if (!$siswa) {
            session()->setFlashdata(['msg' => 'Siswa tidak ditemukan', 'error' => true]);
            return redirect()->back();
         }
         $kelas = $this->getKelasJurusanSlug($siswa['id_kelas']) ?? 'tmp';
         $this->qrCodeFilePath .= "qr-siswa/$kelas/";
         if (!file_exists($this->qrCodeFilePath)) {
            mkdir($this->qrCodeFilePath, 0777, true);
         }
         $filePath = $this->generate(
            nama: $siswa['nama_siswa'],
            nomor: $siswa['nis'],
            unique_code: $siswa['unique_code'],
         );
      } else {
         $guru = (new GuruModel)->find($id);
         if (!$guru) {
            session()->setFlashdata(['msg' => 'Data tidak ditemukan', 'error' => true]);
            return redirect()->back();
         }
         $this->qrCode->setForegroundColor($this->foregroundColor2);
         $this->label->setTextColor($this->foregroundColor2);
         $this->qrCodeFilePath .= 'qr-guru/';
         if (!file_exists($this->qrCodeFilePath)) {
            mkdir($this->qrCodeFilePath, 0777, true);
         }
         $filePath = $this->generate(
            nama: $guru['nama_guru'],
            nomor: $guru['nuptk'],
            unique_code: $guru['unique_code'],
         );
      }

      try {
         return $this->serveQr($filePath, $download);
      } catch (\Throwable $th) {
         session()->setFlashdata(['msg' => $th->getMessage(), 'error' => true]);
         return redirect()->back();
      }
   }

   public function downloadQrSiswa($idSiswa = null)
   {
      return $this->generateAndServe($idSiswa, 'siswa', true);
   }

   public function viewQrSiswa($idSiswa = null)
   {
      return $this->generateAndServe($idSiswa, 'siswa', false);
   }

   public function downloadQrGuru($idGuru = null)
   {
      return $this->generateAndServe($idGuru, 'guru', true);
   }

   public function viewQrGuru($idGuru = null)
   {
      return $this->generateAndServe($idGuru, 'guru', false);
   }

   public function downloadAllQrSiswa()
   {
      $kelas = null;
      if ($idKelas = $this->request->getVar('id_kelas')) {
         $kelas = $this->getKelasJurusanSlug($idKelas);
         if (!$kelas) {
            session()->setFlashdata([
               'msg' => 'Kelas tidak ditemukan',
               'error' => true
            ]);
            return redirect()->back();
         }
      }

      $this->qrCodeFilePath .= "qr-siswa/" . ($kelas ? "{$kelas}/" : '');

      if (!file_exists($this->qrCodeFilePath) || count(glob($this->qrCodeFilePath . '*')) === 0) {
         session()->setFlashdata([
            'msg' => 'QR Code tidak ditemukan, generate qr terlebih dahulu',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         $output = self::UPLOADS_PATH . 'qrcode-siswa' . ($kelas ? "_{$kelas}.zip" : '.zip');

         $this->zipFolder($this->qrCodeFilePath, $output);

         return $this->response->download($output, null, true);
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   public function downloadAllQrGuru()
   {
      $this->qrCodeFilePath .= 'qr-guru/';

      if (!file_exists($this->qrCodeFilePath) || count(glob($this->qrCodeFilePath . '*')) === 0) {
         session()->setFlashdata([
            'msg' => 'QR Code tidak ditemukan, generate qr terlebih dahulu',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         $output = self::UPLOADS_PATH . DIRECTORY_SEPARATOR . 'qrcode-guru.zip';

         $this->zipFolder($this->qrCodeFilePath, $output);

         return $this->response->download($output, null, true);
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   private function zipFolder(string $folder, string $output)
   {
      $zip = new \ZipArchive;
      $zip->open($output, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

      // Create recursive directory iterator
      /** @var \SplFileInfo[] $files */
      $files = new \RecursiveIteratorIterator(
         new \RecursiveDirectoryIterator($folder),
         \RecursiveIteratorIterator::LEAVES_ONLY
      );

      foreach ($files as $file) {
         // Skip directories (they would be added automatically)
         if (!$file->isDir()) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $folderLength = strlen($folder);
            if ($folder[$folderLength - 1] === DIRECTORY_SEPARATOR) {
               $relativePath = substr($filePath, $folderLength);
            } else {
               $relativePath = substr($filePath, $folderLength + 1);
            }

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
         }
      }
      $zip->close();
   }

   protected function kelas(string $unique_code)
   {
      return self::UPLOADS_PATH . DIRECTORY_SEPARATOR . "qr-siswa/{$unique_code}.png";
   }

    protected function getKelasJurusanSlug(string $idKelas)
    {
       $kelas = (new KelasModel)->getKelas($idKelas);
       ;
       if ($kelas) {
          return url_title($kelas->kelas, lowercase: true);
       } else {
          return false;
       }
    }

    public function printQrSiswa($idKelas = null)
    {
       $siswaModel = new SiswaModel();
       $kelasModel = new KelasModel();
       $slugCache = [];

       if ($idKelas) {
          $kelas = $kelasModel->getKelas($idKelas);
          if (!$kelas) {
             session()->setFlashdata(['msg' => 'Kelas tidak ditemukan', 'error' => true]);
             return redirect()->back();
           }
          $siswaList = $siswaModel->getSiswaByKelas($idKelas);
       } else {
          $kelas = null;
          $siswaList = $siswaModel->getAllSiswaWithKelas();
       }

      $items = [];
      foreach ($siswaList as $siswa) {
         $idKelasSiswa = $siswa['id_kelas'];
         if (!isset($slugCache[$idKelasSiswa])) {
            $slugCache[$idKelasSiswa] = $this->getKelasJurusanSlug($idKelasSiswa) ?? 'tmp';
         }
         $kelasSlug = $slugCache[$idKelasSiswa];
         $this->qrCodeFilePath = self::UPLOADS_PATH . "qr-siswa/$kelasSlug/";
         if (!file_exists($this->qrCodeFilePath)) {
            mkdir($this->qrCodeFilePath, 0777, true);
         }
         $this->qrCode->setForegroundColor($this->foregroundColor);
         $this->label->setTextColor($this->foregroundColor);
         $filePath = $this->generate(
            nama: $siswa['nama_siswa'],
            nomor: $siswa['nis'],
            unique_code: $siswa['unique_code'],
         );

         $fileExt = $this->getFileExtension();
         $filename = url_title($siswa['nama_siswa'], lowercase: true) . '_' . url_title($siswa['nis'], lowercase: true) . '.' . $fileExt;
          $items[] = [
             'nama' => $siswa['nama_siswa'],
             'nomor' => $siswa['nis'],
             'nomor_label' => 'NIS',
             'kelas' => $siswa['kelas'] ?? ($kelas->kelas ?? ''),
             'qr_url' => base_url("uploads/qr-siswa/$kelasSlug/$filename"),
          ];
       }

       $groupInfo = $idKelas && $kelas ? 'Kelas : ' . $kelas->kelas : 'Semua Kelas';
       if ($idKelas && $kelas) {
          $groupInfo .= ' - ' . count($items) . ' Siswa';
       } else {
          $groupInfo .= ' - ' . count($items) . ' Siswa';
       }

       $data = [
          'title' => 'Cetak QR Siswa',
          'type' => 'siswa',
          'groupInfo' => $groupInfo,
          'items' => $items,
       ];

       return view('admin/generate-qr/print-qr', $data);
    }

   protected function getFileExtension(): string
   {
      return $this->writer instanceof SvgWriter ? 'svg' : 'png';
   }

   public function printQrSiswaSingle($id)
   {
      $siswa = (new SiswaModel())->find($id);
      if (!$siswa) {
         session()->setFlashdata(['msg' => 'Siswa tidak ditemukan', 'error' => true]);
         return redirect()->back();
      }

      $kelasSlug = $this->getKelasJurusanSlug($siswa['id_kelas']) ?? 'tmp';
      $this->qrCodeFilePath = self::UPLOADS_PATH . "qr-siswa/$kelasSlug/";
      if (!file_exists($this->qrCodeFilePath)) {
         mkdir($this->qrCodeFilePath, 0777, true);
      }
      $this->qrCode->setForegroundColor($this->foregroundColor);
      $this->label->setTextColor($this->foregroundColor);
      $this->generate(
         nama: $siswa['nama_siswa'],
         nomor: $siswa['nis'],
         unique_code: $siswa['unique_code'],
      );

      $fileExt = $this->getFileExtension();
      $filename = url_title($siswa['nama_siswa'], lowercase: true) . '_' . url_title($siswa['nis'], lowercase: true) . '.' . $fileExt;
      $items = [];
      $items[] = [
         'nama' => $siswa['nama_siswa'],
         'nomor' => $siswa['nis'],
         'nomor_label' => 'NIS',
         'kelas' => '',
         'qr_url' => base_url("uploads/qr-siswa/$kelasSlug/$filename"),
      ];

      $data = [
         'title' => 'Cetak QR - ' . $siswa['nama_siswa'],
         'type' => 'siswa',
         'groupInfo' => $siswa['nama_siswa'] . ' (NIS: ' . $siswa['nis'] . ')',
         'items' => $items,
      ];

      return view('admin/generate-qr/print-qr', $data);
   }

   public function printQrGuruSingle($id)
   {
      $guru = (new GuruModel())->find($id);
      if (!$guru) {
         session()->setFlashdata(['msg' => 'Data tidak ditemukan', 'error' => true]);
         return redirect()->back();
      }

      $this->qrCode->setForegroundColor($this->foregroundColor2);
      $this->label->setTextColor($this->foregroundColor2);
      $this->qrCodeFilePath = self::UPLOADS_PATH . 'qr-guru/';
      if (!file_exists($this->qrCodeFilePath)) {
         mkdir($this->qrCodeFilePath, 0777, true);
      }
      $this->generate(
         nama: $guru['nama_guru'],
         nomor: $guru['nuptk'],
         unique_code: $guru['unique_code'],
      );

      $fileExt = $this->getFileExtension();
      $filename = url_title($guru['nama_guru'], lowercase: true) . '_' . url_title($guru['nuptk'], lowercase: true) . '.' . $fileExt;
      $items = [];
      $items[] = [
         'nama' => $guru['nama_guru'],
         'nomor' => $guru['nuptk'],
         'nomor_label' => 'NUPTK',
         'kelas' => '',
         'qr_url' => base_url('uploads/qr-guru/' . $filename),
      ];

      $data = [
         'title' => 'Cetak QR - ' . $guru['nama_guru'],
         'type' => 'guru',
         'groupInfo' => $guru['nama_guru'] . ' (NUPTK: ' . $guru['nuptk'] . ')',
         'items' => $items,
      ];

      return view('admin/generate-qr/print-qr', $data);
   }

     public function printQrGuru()
     {
        $guruList = (new GuruModel())->getAllGuru();

        $items = [];
        foreach ($guruList as $guru) {
           $this->qrCode->setForegroundColor($this->foregroundColor2);
           $this->label->setTextColor($this->foregroundColor2);
           $this->qrCodeFilePath = self::UPLOADS_PATH . 'qr-guru/';
           if (!file_exists($this->qrCodeFilePath)) {
              mkdir($this->qrCodeFilePath, 0777, true);
           }
           $filePath = $this->generate(
              nama: $guru['nama_guru'],
              nomor: $guru['nuptk'],
              unique_code: $guru['unique_code'],
           );

           $fileExt = $this->getFileExtension();
           $filename = url_title($guru['nama_guru'], lowercase: true) . '_' . url_title($guru['nuptk'], lowercase: true) . '.' . $fileExt;
          $items[] = [
             'nama' => $guru['nama_guru'],
             'nomor' => $guru['nuptk'],
             'nomor_label' => 'NUPTK',
             'kelas' => '',
             'qr_url' => base_url('uploads/qr-guru/' . $filename),
          ];
       }

       $data = [
          'title' => 'Cetak QR Guru',
          'type' => 'guru',
          'groupInfo' => 'Semua Guru - ' . count($items) . ' Guru',
          'items' => $items,
       ];

       return view('admin/generate-qr/print-qr', $data);
    }
}
