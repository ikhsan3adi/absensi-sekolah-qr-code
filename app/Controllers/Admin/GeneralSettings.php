<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GeneralSettingsModel;

class GeneralSettings extends BaseController
{
    protected $generalSettingsModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->generalSettingsModel = new GeneralSettingsModel();
    }

    public function index()
    {
        $data['title'] = 'Pengaturan Utama';
        $data['ctx'] = 'general_settings';

        return view('admin/general-settings/index', $data);
    }

    public function generalSettingsPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('school_name', 'Nama Sekolah', 'required|max_length[200]');
        $val->setRule('school_year', 'Tahun Ajaran', 'required|max_length[200]');
        $val->setRule('copyright', 'copyright', 'max_length[200]');

        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to('admin/general-settings')->withInput();
        } else {
            if ($this->generalSettingsModel->updateSettings()) {
                $this->session->setFlashdata('success', 'Data berhasil diubah');
            } else {
                $this->session->setFlashdata('error', 'Error data!');
            }
        }
        return redirect()->to('admin/general-settings');
    }
}
