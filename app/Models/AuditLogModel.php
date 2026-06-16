<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'tb_audit_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_user', 'aksi', 'tabel', 'id_record', 'data_lama', 'data_baru', 'ip_address', 'created_at'];
    protected $useTimestamps = false;

    public function log($aksi, $tabel = null, $idRecord = null, $oldData = null, $newData = null)
    {
        $this->insert([
            'id_user' => user_id(),
            'aksi' => $aksi,
            'tabel' => $tabel,
            'id_record' => $idRecord,
            'data_lama' => $oldData ? json_encode($oldData) : null,
            'data_baru' => $newData ? json_encode($newData) : null,
            'ip_address' => service('request')->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getLogs()
    {
        return $this->select('tb_audit_logs.*, users.username')
            ->join('users', 'users.id = tb_audit_logs.id_user', 'left')
            ->orderBy('created_at', 'DESC')
            ->limit(500)
            ->findAll();
    }
}
