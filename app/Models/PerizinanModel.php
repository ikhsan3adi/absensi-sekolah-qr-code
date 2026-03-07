<?php

namespace App\Models;

use CodeIgniter\Model;

class PerizinanModel extends Model
{
    protected $table = 'tb_perizinan';
    protected $primaryKey = 'id_perizinan';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_siswa',
        'id_guru',
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe_izin',
        'alasan',
        'bukti',
        'status',
        'id_petugas'
    ];

    public function getPerizinanWithSiswa($status = null)
    {
        $builder = $this->db->table($this->table)
            ->select('tb_perizinan.*, tb_siswa.nama_siswa, tb_siswa.nis, tb_kelas.tingkat, tb_jurusan.jurusan, tb_kelas.index_kelas, tb_guru.nama_guru, tb_guru.nuptk')
            ->join('tb_siswa', 'tb_siswa.id_siswa = tb_perizinan.id_siswa', 'left')
            ->join('tb_kelas', 'tb_kelas.id_kelas = tb_siswa.id_kelas', 'left')
            ->join('tb_jurusan', 'tb_jurusan.id = tb_kelas.id_jurusan', 'left')
            ->join('tb_guru', 'tb_guru.id_guru = tb_perizinan.id_guru', 'left');

        if ($status) {
            $builder->where('tb_perizinan.status', $status);
        }

        return $builder->orderBy('tb_perizinan.created_at', 'DESC')->get()->getResultArray();
    }

    public function getPerizinanBySiswa($id_siswa)
    {
        return $this->where('id_siswa', $id_siswa)->orderBy('created_at', 'DESC')->findAll();
    }

    public function konfirmasiPerizinan($id_perizinan, $status, $id_petugas)
    {
        $perizinan = $this->find($id_perizinan);
        if (!$perizinan) {
            return ['status' => 'error', 'message' => 'Data tidak ditemukan.'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Update status perizinan
        $this->update($id_perizinan, [
            'status' => $status,
            'id_petugas' => $id_petugas
        ]);

        if ($status === 'Disetujui') {
            $id_kehadiran = ($perizinan['tipe_izin'] === 'Sakit') ? 2 : 3;
            $start = new \DateTime($perizinan['tanggal_mulai']);
            $end = new \DateTime($perizinan['tanggal_selesai']);
            $interval = new \DateInterval('P1D');
            $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));

            if (!empty($perizinan['id_siswa'])) {
                // LOGIKA SISWA
                $siswaModel = new \App\Models\SiswaModel();
                $presensiSiswaModel = new \App\Models\PresensiSiswaModel();
                $siswa = $siswaModel->find($perizinan['id_siswa']);

                foreach ($period as $date) {
                    $tanggal = $date->format('Y-m-d');
                    $existing = $presensiSiswaModel->getPresensiByIdSiswaTanggal($siswa['id_siswa'], $tanggal);
                    $dataPresensi = [
                        'id_siswa' => $siswa['id_siswa'],
                        'id_kelas' => $siswa['id_kelas'],
                        'tanggal' => $tanggal,
                        'id_kehadiran' => $id_kehadiran,
                        'keterangan' => $perizinan['alasan']
                    ];
                    if ($existing) {
                        $presensiSiswaModel->update($existing['id_presensi'], $dataPresensi);
                    } else {
                        $presensiSiswaModel->insert($dataPresensi);
                    }
                }
            } elseif (!empty($perizinan['id_guru'])) {
                // LOGIKA GURU
                $presensiGuruModel = new \App\Models\PresensiGuruModel();
                foreach ($period as $date) {
                    $tanggal = $date->format('Y-m-d');
                    $existing = $presensiGuruModel->getPresensiByIdGuruTanggal($perizinan['id_guru'], $tanggal);
                    $dataPresensi = [
                        'id_guru' => $perizinan['id_guru'],
                        'tanggal' => $tanggal,
                        'id_kehadiran' => $id_kehadiran,
                        'keterangan' => $perizinan['alasan']
                    ];
                    if ($existing) {
                        $presensiGuruModel->update($existing['id_presensi'], $dataPresensi);
                    } else {
                        $presensiGuruModel->insert($dataPresensi);
                    }
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['status' => 'error', 'message' => 'Terjadi kesalahan sistem saat memperbarui presensi.'];
        }

        return ['status' => 'success', 'message' => 'Status perizinan berhasil diperbarui.'];
    }
}
