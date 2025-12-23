<?php

namespace App\Validation;

use App\Models\SiswaModel;
use App\Models\GuruModel;

class RFIDRules
{
    /**
     * Checks if an RFID code is unique across tb_siswa and tb_guru.
     * Use as: is_rfid_unique[exclude_id,type]
     * Example: is_rfid_unique[1,siswa] or is_rfid_unique[,siswa]
     */
    public function is_rfid_unique(string $str, string $fields, array $data, &$error = null): bool
    {
        if (empty($str)) {
            return true;
        }

        $params = explode(',', $fields);
        $excludeId = $params[0] ?? null;
        $type = $params[1] ?? null;

        $siswaModel = new SiswaModel();
        $guruModel = new GuruModel();

        // Check in tb_siswa
        $siswaQuery = $siswaModel->where('rfid_code', $str);
        if ($type === 'siswa' && !empty($excludeId)) {
            $siswaQuery->where('id_siswa !=', $excludeId);
        }
        if ($siswaQuery->countAllResults() > 0) {
            $error = 'RFID code ini sudah digunakan oleh Siswa.';
            return false;
        }

        // Check in tb_guru
        $guruQuery = $guruModel->where('rfid_code', $str);
        if ($type === 'guru' && !empty($excludeId)) {
            $guruQuery->where('id_guru !=', $excludeId);
        }
        if ($guruQuery->countAllResults() > 0) {
            $error = 'RFID code ini sudah digunakan oleh Guru.';
            return false;
        }

        return true;
    }
}
