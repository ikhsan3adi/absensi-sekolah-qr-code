<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class QRGenerator extends BaseController
{
   protected QrCode $qrCode;
   protected PngWriter $writer;
   protected ?Logo $logo;
   protected Label $label;
   protected Font $labelFont;
   protected Color $foregroundColor;
   protected Color $foregroundColor2;
   protected Color $backgroundColor;

   protected string $qrCodeFilePath;

   public function __construct()
   {
      $PUBLIC_PATH = ROOTPATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
      $this->qrCodeFilePath = $PUBLIC_PATH . 'uploads' . DIRECTORY_SEPARATOR;

      if (!file_exists($this->qrCodeFilePath)) mkdir($this->qrCodeFilePath);

      $this->writer = new PngWriter();

      $this->labelFont = new Font($PUBLIC_PATH . 'assets/fonts/Roboto-Medium.ttf', 14);

      $this->foregroundColor = new Color(44, 73, 162);
      $this->foregroundColor2 = new Color(28, 101, 90);
      $this->backgroundColor = new Color(255, 255, 255);

      // Create logo
      $this->logo = boolval(env('QR_LOGO'))
         ? Logo::create($PUBLIC_PATH . 'assets/img/logo_sekolah.jpg')->setResizeToWidth(75)
         : null;

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

   public function generateQrSiswa()
   {
      $kelas = url_title($this->request->getVar('kelas'), '-', true);

      $this->qrCodeFilePath .= "qr-siswa/$kelas/";

      if (!file_exists($this->qrCodeFilePath)) {
         mkdir($this->qrCodeFilePath, recursive: true);
      }

      $this->generate(
         unique_code: $this->request->getVar('unique_code'),
         nama: $this->request->getVar('nama'),
         nomor: $this->request->getVar('nomor')
      );

      return $this->response->setJSON(true);
   }

   public function generateQrGuru()
   {
      $this->qrCode->setForegroundColor($this->foregroundColor2);
      $this->label->setTextColor($this->foregroundColor2);

      $this->qrCodeFilePath .= 'qr-guru/';

      if (!file_exists($this->qrCodeFilePath)) {
         mkdir($this->qrCodeFilePath);
      }

      $this->generate(
         unique_code: $this->request->getVar('unique_code'),
         nama: $this->request->getVar('nama'),
         nomor: $this->request->getVar('nomor')
      );

      return $this->response->setJSON(true);
   }

   protected function generate($nama, $nomor, $unique_code)
   {
      $filename = url_title($nama, separator: '-', lowercase: true) . "_" . url_title($nomor, separator: '-', lowercase: true) . '.png';

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
   }
}
