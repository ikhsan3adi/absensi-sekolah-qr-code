<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->get('/', 'Scan::index');
$routes->get('/scan', 'Scan::index');
$routes->get('/scan/masuk', 'Scan::index/Masuk');
$routes->get('/scan/pulang', 'Scan::index/Pulang');

$routes->post('/cek', 'Scan::cek_kode');

// admin dashboard
$routes->get('/admin', 'Admin\Dashboard::index');
$routes->get('/admin/dashboard', 'Admin\Dashboard::index');

// Admin
$routes->group('admin', function ($routes) {
    // admin lihat data siswa & guru
    $routes->get('data-siswa', 'Admin\LihatData::lihat_data_siswa');
    $routes->post('data-siswa', 'Admin\LihatData::ambil_data_siswa');
    $routes->get('data-siswa/edit/(:any)', 'Admin\LihatData::form_edit_siswa/$1');

    $routes->get('data-guru', 'Admin\LihatData::lihat_data_guru');
    $routes->post('data-guru', 'Admin\LihatData::ambil_data_guru');

    // admin lihat data absen siswa
    $routes->get('absen-siswa', 'Admin\LihatDataAbsenSiswa::index');
    $routes->post('absen-siswa', 'Admin\LihatDataAbsenSiswa::ambil_siswa'); // ambil siswa berdasarkan kelas dan tanggal
    $routes->post('absen-siswa/edit', 'Admin\LihatDataAbsenSiswa::ubah_kehadiran'); // ubah kehadiran siswa

    // admin lihat data absen siswa
    $routes->get('absen-guru', 'Admin\LihatDataAbsenGuru::index');
    $routes->post('absen-guru', 'Admin\LihatDataAbsenGuru::ambil_guru'); // ambil guru berdasarkan tanggal
    $routes->post('absen-guru/edit', 'Admin\LihatDataAbsenGuru::ubah_kehadiran'); // ambil guru berdasarkan tanggal
});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
