<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HariLiburModel;

class Holiday extends BaseController
{
    protected $holidayModel;

    public function __construct()
    {
        $this->holidayModel = new HariLiburModel();
    }

    public function index()
    {
        if ($denied = $this->denyUnlessPermitted()) {
            return $denied;
        }

        $month = request()->getGet('month') ?? date('m');
        $year = request()->getGet('year') ?? date('Y');

        $data = [
            'title' => 'Manajemen Hari Libur',
            'ctx' => 'holiday',
            'holidays' => $this->holidayModel->getHolidaysByMonth($month, $year),
            'selectedMonth' => $month,
            'selectedYear' => $year
        ];

        return view('admin/holiday/index', $data);
    }

    public function generateWeekend()
    {
        if ($denied = $this->denyUnlessPermitted()) {
            return $denied;
        }

        $month = date('m');
        $year = date('Y');
        
        $daysInMonth = date('t', strtotime("$year-$month-01"));
        $count = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

            if (!isWorkingDay($date)) {
                if (!$this->holidayModel->where('tanggal', $date)->first()) {
                    $dayOfWeek = date('w', strtotime($date));
                    $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu'];
                    $this->holidayModel->insert([
                        'tanggal' => $date,
                        'keterangan' => 'Hari ' . $dayNames[$dayOfWeek]
                    ]);
                    $count++;
                }
            }
        }

        return redirect()->to(base_url('admin/holiday'))->with('success', "$count hari non-kerja berhasil ditambahkan untuk bulan ini.");
    }

    public function save()
    {
        if ($denied = $this->denyUnlessPermitted()) {
            return $denied;
        }

        $tanggal_mulai = request()->getPost('tanggal_mulai');
        $tanggal_selesai = request()->getPost('tanggal_selesai');
        $keterangan = request()->getPost('keterangan');

        if (!$tanggal_mulai || !$tanggal_selesai) {
            return redirect()->back()->with('error', 'Tanggal mulai dan selesai harus diisi.');
        }

        // Iterasi dari tanggal_mulai sampai tanggal_selesai
        $start = new \DateTime($tanggal_mulai);
        $end = new \DateTime($tanggal_selesai);
        
        if ($start > $end) {
            return redirect()->back()->with('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.');
        }

        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));

        $count = 0;
        foreach ($period as $date) {
            $tgl = $date->format('Y-m-d');
            
            // Cek jika belum ada, maka insert
            if (!$this->holidayModel->where('tanggal', $tgl)->first()) {
                $this->holidayModel->insert([
                    'tanggal' => $tgl,
                    'keterangan' => $keterangan
                ]);
                $count++;
            }
        }

        return redirect()->to(base_url('admin/holiday'))->with('success', "$count hari libur berhasil ditambahkan.");
    }

    public function delete($id)
    {
        if ($denied = $this->denyUnlessPermitted()) {
            return $denied;
        }

        $this->holidayModel->delete($id);
        return redirect()->to(base_url('admin/holiday'))->with('success', 'Hari libur berhasil dihapus.');
    }

    public function bulkDelete()
    {
        if ($denied = $this->denyUnlessPermitted()) {
            return $denied;
        }

        $ids = request()->getPost('holiday_ids');
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih hari libur yang ingin dihapus.');
        }

        $this->holidayModel->whereIn('id', $ids)->delete();
        return redirect()->to(base_url('admin/holiday'))->with('success', count($ids) . ' hari libur berhasil dihapus.');
    }

    private function denyUnlessPermitted()
    {
        helper('user_helper');
        $user = user();
        if ($user !== null && $user->can('holiday.manage')) {
            return null;
        }

        return $this->response->setStatusCode(403)->setBody('Forbidden');
    }
}
