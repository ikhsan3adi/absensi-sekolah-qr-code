<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class AttendanceSeeder extends Seeder
{
  public function run()
  {
    $db = \Config\Database::connect();
    $siswa = $db->table('tb_siswa')->get()->getResultArray();
    $guru = $db->table('tb_guru')->get()->getResultArray();

    $daysToSeed = 7;
    $now = Time::now();

    $presensiSiswaBatch = [];
    $presensiGuruBatch = [];

    for ($i = 0; $i < $daysToSeed; $i++) {
      $date = $now->subDays($i)->toDateString();

      // Skip weekends (optional, but realistic)
      $dayOfWeek = date('N', strtotime($date));
      if ($dayOfWeek >= 6)
        continue;

      // Seed Siswa
      foreach ($siswa as $s) {
        // Check if already seeded for this date
        $exists = $db->table('tb_presensi_siswa')
          ->where(['id_siswa' => $s['id_siswa'], 'tanggal' => $date])
          ->countAllResults();

        if ($exists > 0)
          continue;

        $rand = rand(1, 100);
        $idKehadiran = 1; // Default Hadir
        $jamMasuk = null;
        $jamKeluar = null;
        $keterangan = '';

        if ($rand <= 85) {
          $idKehadiran = 1; // Hadir
          $jamMasuk = sprintf('%02d:%02d:00', rand(6, 7), rand(0, 59));
          $jamKeluar = sprintf('%02d:%02d:00', rand(14, 15), rand(0, 59));
        } elseif ($rand <= 92) {
          $idKehadiran = 3; // Izin
          $keterangan = 'Ada keperluan keluarga';
        } elseif ($rand <= 97) {
          $idKehadiran = 2; // Sakit
          $keterangan = 'Demam / Flu';
        } else {
          $idKehadiran = 4; // Tanpa keterangan
        }

        $presensiSiswaBatch[] = [
          'id_siswa' => $s['id_siswa'],
          'id_kelas' => $s['id_kelas'],
          'tanggal' => $date,
          'jam_masuk' => $jamMasuk,
          'jam_keluar' => $jamKeluar,
          'id_kehadiran' => $idKehadiran,
          'keterangan' => $keterangan
        ];
      }

      // Seed Guru
      foreach ($guru as $g) {
        $exists = $db->table('tb_presensi_guru')
          ->where(['id_guru' => $g['id_guru'], 'tanggal' => $date])
          ->countAllResults();

        if ($exists > 0)
          continue;

        $rand = rand(1, 100);
        $idKehadiran = 1;
        $jamMasuk = null;
        $jamKeluar = null;
        $keterangan = '';

        if ($rand <= 90) { // Teachers are more disciplined? :)
          $idKehadiran = 1;
          $jamMasuk = sprintf('%02d:%02d:00', rand(6, 7), rand(0, 30));
          $jamKeluar = sprintf('%02d:%02d:00', rand(15, 16), rand(0, 59));
        } elseif ($rand <= 95) {
          $idKehadiran = 3;
          $keterangan = 'Urusan dinas';
        } elseif ($rand <= 98) {
          $idKehadiran = 2;
          $keterangan = 'Kurang enak badan';
        } else {
          $idKehadiran = 4;
        }

        $presensiGuruBatch[] = [
          'id_guru' => $g['id_guru'],
          'tanggal' => $date,
          'jam_masuk' => $jamMasuk,
          'jam_keluar' => $jamKeluar,
          'id_kehadiran' => $idKehadiran,
          'keterangan' => $keterangan
        ];
      }
    }

    if (!empty($presensiSiswaBatch)) {
      $db->table('tb_presensi_siswa')->insertBatch($presensiSiswaBatch);
    }

    if (!empty($presensiGuruBatch)) {
      $db->table('tb_presensi_guru')->insertBatch($presensiGuruBatch);
    }

    echo "Seeding completed. " . count($presensiSiswaBatch) . " siswa and " . count($presensiGuruBatch) . " guru attendance records added.\n";
  }
}
