<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class School extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Nama Sekolah
     * --------------------------------------------------------------------------
     *
     * Pengaturan untuk nama sekolah
     */
    public string $name = 'SMK 1 Indonesia';

    /**
     * --------------------------------------------------------------------------
     * Tahun Ajaran
     * --------------------------------------------------------------------------
     *
     * Pengaturan untuk tahun ajaran
     */
    public string $schoolYear = '2024/2025';
}
