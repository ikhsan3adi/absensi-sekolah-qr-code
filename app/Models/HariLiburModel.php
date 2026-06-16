<?php

namespace App\Models;

use CodeIgniter\Model;

class HariLiburModel extends Model
{
    protected $table = 'tb_hari_libur';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['tanggal', 'keterangan'];

    public function isHoliday($date)
    {
        return $this->where('tanggal', $date)->first() !== null;
    }

    public function getAllHolidays()
    {
        return $this->orderBy('tanggal', 'DESC')->findAll();
    }

    public function getHolidaysByMonth($month, $year)
    {
        return $this->where('MONTH(tanggal)', $month)
            ->where('YEAR(tanggal)', $year)
            ->orderBy('tanggal', 'ASC')
            ->findAll();
    }
}
