# Migration & Seeder Guide

Panduan lengkap untuk mengelola database migrations dan seeders pada aplikasi Absensi Sekolah QR Code.

## Daftar Isi

- [Tentang Migrations](#tentang-migrations)
- [Migrasi Auth Library](#migrasi-auth-library)
- [Struktur Groups & Permissions](#struktur-groups--permissions)
- [Tentang Seeders](#tentang-seeders)
- [Urutan Migration](#urutan-migration)
- [Cara Penggunaan](#cara-penggunaan)
- [Troubleshooting](#troubleshooting)

## Tentang Migrations

Migration adalah cara version control untuk database. File migration berada di `app/Database/Migrations/` dan dijalankan berurutan berdasarkan timestamp filename.

### Daftar Migration Files

1. **2023-08-18-000001_CreateJurusanTable.php**
   - Membuat tabel `tb_jurusan` (jurusan sekolah)
   - Kolom: id, jurusan, timestamps

2. **2023-08-18-000002_CreateKelasTable.php**
   - Membuat tabel `tb_kelas` (kelas)
   - Kolom: id_kelas, tingkat, id_jurusan, index_kelas, timestamps
   - Foreign key: id_jurusan -> tb_jurusan(id)

3. **2023-08-18-000003_CreateDB.php**
   - Membuat 5 tabel utama:
     - `tb_kehadiran` - Master status kehadiran
     - `tb_guru` - Data guru
     - `tb_siswa` - Data siswa
     - `tb_presensi_guru` - Data presensi guru
     - `tb_presensi_siswa` - Data presensi siswa
   - Menambahkan foreign keys antar tabel

4. **2023-08-18-000004_AddSuperadmin.php**
   - [LEGACY] Migration lama dari MythAuth
   - Menambah kolom `is_superadmin` ke tabel `users`
   - Kolom ini telah dihapus oleh migrasi ke-8

5. **2024-07-24-083011_GeneralSettings.php**
   - Membuat tabel `general_settings` untuk konfigurasi aplikasi
   - Kolom: id, logo, school_name, school_year, copyright

6. **2025-12-23-000001_AddRfidToSiswaGuru.php**
   - Menambah kolom `rfid_code` ke tabel `tb_siswa` dan `tb_guru`
   - Menambah index untuk performa pencarian

7. **2025-12-23-000002_AddWaliKelasToKelas.php**
   - Menambah kolom `id_wali_kelas` ke tabel `tb_kelas`
   - Menambah kolom `id_guru` ke tabel `users`
   - Foreign keys: id_wali_kelas -> tb_guru(id_guru), id_guru -> tb_guru(id_guru)

8. **2025-12-24-000001_MigrateIsSuperadminToGroups.php**
   - **BREAKING CHANGE**: Migrasi dari MythAuth ke CodeIgniter Shield groups
   - Membaca nilai `is_superadmin` dari setiap user dan memetakannya ke Shield group:
     - `0` (Scanner) -> group `scanner`
     - `1` (Super Admin) -> group `superadmin` + `admin`
     - `2` (Kepsek) -> group `kepsek`
     - `3` (Staf Petugas) -> group `admin`
   - Jika user memiliki `id_guru` (profil guru), ditambahkan group `guru`
   - Menghapus kolom `is_superadmin` dari tabel `users`

9. **2026-03-07-000001_CreatePerizinanTable.php**
   - Membuat tabel `tb_perizinan` (pengajuan izin/sakit digital)
   - Kolom: id_perizinan, nis, nuptk, tipe (Izin/Sakit), tanggal_mulai, tanggal_selesai, alasan, bukti (file path), status (Pending/Disetujui/Ditolak), created_at, updated_at

10. **2026-03-07-000002_AddLateSystemFields.php**
    - Menambah kolom ke tabel `tb_presensi_siswa`:
      - `poin_keterlambatan` - Akumulasi poin keterlambatan (integer, default 0)
      - `late_minutes` - Jumlah menit keterlambatan
    - Menambah kolom `batas_jam_masuk` ke tabel `general_settings`

11. **2026-03-07-000003_CreateHariLiburTable.php**
    - Membuat tabel `tb_hari_libur` (manajemen hari libur)
    - Kolom: id, tanggal_mulai, tanggal_selesai, keterangan, created_at, updated_at

12. **2026-03-07-000004_AddJamPulangStandard.php**
    - Menambah kolom `batas_jam_pulang` ke tabel `general_settings`
    - Digunakan untuk menentukan batas waktu status "Belum Scan" berubah menjadi "Alfa"

13. **2026-03-07-000005_AddGuruToPerizinan.php**
    - Menambah kolom `nip` ke tabel `tb_perizinan` untuk mendukung pengajuan izin guru
    - Foreign key: nip -> tb_guru(nip)

14. **2026-03-07-000006_CreateAuditLogsTable.php**
    - Membuat tabel `audit_logs` (log aktivitas sistem)
    - Kolom: id, user_id, action, resource, resource_id, old_values (JSON), new_values (JSON), ip_address, user_agent, created_at

15. **2026-06-17-000001_AddWorkingDays.php**
    - Menambah kolom `hari_kerja` ke tabel `general_settings`
    - Menyimpan konfigurasi hari kerja sebagai string angka hari (1=Senin..7=Minggu), default `1,2,3,4,5` (Senin-Jumat)

## Migrasi Auth Library

Aplikasi ini sebelumnya menggunakan **MythAuth** untuk autentikasi dan otorisasi. Telah dimigrasi ke **CodeIgniter Shield**.

### Perubahan Database

- Kolom `is_superadmin` pada tabel `users` dihapus
- Role disimpan di tabel `auth_groups_users` (milik Shield) sebagai group name string
- User dapat memiliki multiple groups (contoh: superadmin + admin, atau admin + guru)
- Group `guru` otomatis ditambahkan saat user memiliki `id_guru`

### Perubahan Code

- `$user->is_superadmin` tidak lagi tersedia. Gunakan `$user->inGroup('superadmin')` atau `auth()->user()->can('permission.name')`
- `user_role()` mengembalikan string group name, bukan objek UserRole
- `is_wali_kelas()` sekarang cek ke tabel `tb_kelas.id_wali_kelas`, bukan cek `id_guru`
- `is_guru()` fungsi baru untuk cek apakah user memiliki profil guru (`id_guru`)

### Helper Functions yang Tersedia

```php
user()              // auth()->user()
user_role()         // Group name string (superadmin > admin > kepsek > scanner > guru)
getUserRole()       // Label untuk display
is_superadmin()     // Cek group superadmin
is_guru()           // Cek kepemilikan id_guru
is_wali_kelas()     // Cek tb_kelas.id_wali_kelas
is_kepsek()         // Cek group kepsek
can_edit_attendance()  // Cek permission attendance.edit
can_generate_qr()      // Cek permission qr.generate
can_view_report()      // Cek permission attendance.view
```

## Struktur Groups & Permissions

Groups dan permissions didefinisikan di `app/Config/AuthGroups.php`.

### Groups

| Group Key | Title | Deskripsi |
|-----------|-------|-----------|
| superadmin | Super Admin | Akses penuh ke seluruh fitur aplikasi |
| admin | Staf Petugas | Mengelola absensi, generate QR, dan laporan |
| kepsek | Kepala Sekolah | Melihat laporan absensi |
| scanner | Scanner | Hanya scan QR untuk presensi |
| guru | Guru | Guru/Wali Kelas, mengelola presensi siswanya |

### Permissions

| Permission | Grup Pemilik |
|------------|-------------|
| dashboard.view-admin | superadmin, admin, kepsek |
| students.manage | superadmin |
| teachers.manage | superadmin |
| classes.manage | superadmin |
| attendance.edit | superadmin, admin, guru |
| attendance.view | superadmin, admin, kepsek, guru |
| qr.generate | superadmin, admin |
| petugas.manage | superadmin |
| settings.manage | superadmin |
| backup.manage | superadmin |
| teacher.access | superadmin, guru |

### Route-Level Protection

Tiap route group dilindungi oleh `PermissionFilter` milik Shield:

```
admin/absen-siswa/*     -> permission:attendance.edit
admin/siswa/*           -> permission:students.manage
admin/guru/*            -> permission:teachers.manage
admin/kelas/*           -> permission:classes.manage
admin/petugas/*         -> permission:petugas.manage
admin/generate/*        -> permission:qr.generate
admin/laporan/*         -> permission:attendance.view
admin/general-settings/* -> permission:settings.manage
admin/backup/*          -> permission:backup.manage
teacher/*               -> permission:teacher.access
```

### Catatan Multiple Groups

User dapat memiliki lebih dari satu group. Saat menyimpan petugas:

- `savePetugas()` menggunakan `syncGroups()` yang mengganti seluruh group user
- Jika `id_guru` diisi, group `guru` otomatis ditambahkan
- Contoh: superadmin yang juga guru akan memiliki groups: superadmin, admin, guru

## Tentang Seeders

Seeder digunakan untuk mengisi data awal ke database. File seeder berada di `app/Database/Seeds/`.

### Daftar Seeder Files

1. **DatabaseSeeder.php** (Main Seeder)
   - Menjalankan semua seeder dalam urutan yang benar
2. **KehadiranSeeder.php**
   - Mengisi data master kehadiran: Hadir, Sakit, Izin, Tanpa keterangan

3. **JurusanSeeder.php**
   - Mengisi data jurusan: OTKP, BDP, AKL, RPL

4. **KelasSeeder.php**
   - Mengisi data kelas awal: X, XI, XII untuk semua jurusan

5. **SuperadminSeeder.php**
   - Membuat akun superadmin default
   - Username: `superadmin`
   - Password: `superadmin`
   - Email: `adminsuper@gmail.com`
   - Menggunakan Shield `auth()->getProvider()` (bukan MythAuth)
   - Menambahkan user ke group `superadmin` dan `admin`

6. **GeneralSettingsSeeder.php**
   - Mengisi pengaturan umum aplikasi
   - School Name: SMK 1 Indonesia
   - School Year: 2024/2025

7. **GuruSeeder.php** (Optional)
   - Contoh seeder untuk data guru (sudah ada)

8. **SiswaSeeder.php** (Optional)
   - Contoh seeder untuk data siswa (sudah ada)

## Urutan Migration

Migration akan dijalankan berdasarkan urutan timestamp berikut:

```
1. CreateJurusanTable        (tb_jurusan)
2. CreateKelasTable          (tb_kelas) -> referensi tb_jurusan
3. CreateDB                  (5 tabel + foreign keys)
   - tb_kehadiran
   - tb_guru
   - tb_siswa            -> referensi tb_kelas
   - tb_presensi_guru    -> referensi tb_guru, tb_kehadiran
   - tb_presensi_siswa   -> referensi tb_siswa, tb_kelas, tb_kehadiran
4. AddSuperadmin            (LEGACY - migration MythAuth)
5. GeneralSettings          (general_settings)
6. AddRfidToSiswaGuru       (kolom rfid_code ke tb_siswa & tb_guru)
7. AddWaliKelasToKelas      (kolom id_wali_kelas ke tb_kelas, id_guru ke users)
8. MigrateIsSuperadminToGroups (BREAKING - hapus is_superadmin, migrasi ke Shield)
9. CreatePerizinanTable        (tb_perizinan)
10. AddLateSystemFields         (kolom poin_keterlambatan, late_minutes, batas_jam_masuk)
11. CreateHariLiburTable        (tb_hari_libur)
12. AddJamPulangStandard        (kolom batas_jam_pulang)
13. AddGuruToPerizinan          (kolom nip di tb_perizinan)
14. CreateAuditLogsTable        (audit_logs)
15. AddWorkingDays              (kolom hari_kerja di general_settings)
```

## Cara Penggunaan

### Fresh Installation (Database Baru)

1. **Buat database** di MySQL/MariaDB:

   ```sql
   CREATE DATABASE db_absensi;
   ```

2. **Konfigurasi `.env`**:

   ```env
   database.default.hostname = localhost
   database.default.database = db_absensi
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   ```

3. **Jalankan semua migration**:

   ```bash
   php spark migrate --all
   ```

4. **Jalankan semua seeder**:

   ```bash
   php spark db:seed DatabaseSeeder
   ```

5. **Verifikasi**:
   - Login menggunakan username: `superadmin` password: `superadmin`

### Reset Database (Hati-hati! Menghapus semua data)

1. **Rollback semua migration**:

   ```bash
   php spark migrate:rollback
   ```

2. **Jalankan ulang migration**:

   ```bash
   php spark migrate --all
   ```

3. **Seed ulang data**:
   ```bash
   php spark db:seed DatabaseSeeder
   ```

### Jalankan Seeder Spesifik

```bash
# Jalankan seeder individual
php spark db:seed KehadiranSeeder
php spark db:seed JurusanSeeder
php spark db:seed SuperadminSeeder

# Atau jalankan semua sekaligus
php spark db:seed DatabaseSeeder
```

### Check Status Migration

```bash
# Lihat status migration
php spark migrate:status

# Lihat migration history
php spark migrate:version
```

### Rollback Migration Tertentu

```bash
# Rollback 1 batch terakhir
php spark migrate:rollback

# Rollback semua migration
php spark migrate:rollback -all

# Rollback ke batch tertentu
php spark migrate:rollback -b 3
```

## Troubleshooting

### Error: Unknown column 'email' in 'where clause'

**Penyebab**: Shield menyimpan email di tabel `auth_identities.secret`, bukan kolom `users.email`.

**Solusi**: Gunakan `$userProvider->findByCredentials(['email' => $email])` untuk mencari user by email, bukan `$userProvider->where('email', ...)`.

### Error: Foreign key constraint fails

**Penyebab**: Mencoba menjalankan migration tidak berurutan atau tabel parent belum ada.

**Solusi**:

```bash
# Reset semua migration
php spark migrate:rollback -all

# Jalankan ulang dari awal
php spark migrate --all
```

### Error: Table already exists

**Penyebab**: Tabel sudah ada di database dari instalasi sebelumnya.

**Solusi 1** (Hapus manual):

```sql
DROP DATABASE db_absensi;
CREATE DATABASE db_absensi;
```

**Solusi 2** (Reset migration):

```bash
php spark migrate:refresh
```

### Error: Class "Myth\Auth\Password" not found

**Penyebab**: MythAuth sudah tidak digunakan. Semua kode autentikasi telah migrasi ke CodeIgniter Shield.

**Solusi**: Pastikan `composer.json` hanya memiliki `codeigniter4/shield` (tidak perlu `myth/auth`). Jalankan ulang migration:

```bash
composer install
php spark migrate --all
```

### Seeder Tidak Jalan

**Penyebab**: File seeder tidak ditemukan atau nama class salah.

**Solusi**:

```bash
# Pastikan namespace dan nama class benar
# Namespace: App\Database\Seeds
# Class name harus sama dengan filename

# Clear cache
php spark cache:clear
```

### Data Ganda Setelah Seed Ulang

**Penyebab**: Seeder dijalankan berkali-kali tanpa reset database.

**Solusi**: Seeder sudah dilengkapi dengan pengecekan data existing, tapi untuk hasil clean:

```bash
# Truncate tabel terlebih dahulu
php spark db:table tb_jurusan --truncate
php spark db:table tb_kehadiran --truncate

# Atau reset total
php spark migrate:refresh
php spark db:seed DatabaseSeeder
```

## Best Practices

1. **Selalu backup database** sebelum menjalankan migration di production
2. **Test migration** di environment development terlebih dahulu
3. **Jangan edit migration yang sudah di-run** di production
4. **Buat migration baru** untuk perubahan schema
5. **Gunakan seeder** untuk data master, bukan untuk data transaksional
6. **Version control** semua migration dan seeder files
7. **Dokumentasikan perubahan** di CHANGELOG atau commit message

## Keamanan

1. **Ubah password superadmin** setelah instalasi pertama:
   - Edit `app/Database/Seeds/SuperadminSeeder.php` sebelum seed
   - Atau ubah lewat aplikasi setelah login

2. **Jangan commit** file `.env` ke repository

3. **Gunakan environment variables** untuk credentials database

## Referensi

- [CodeIgniter 4 Migrations Documentation](https://codeigniter.com/user_guide/dbmgmt/migration.html)
- [CodeIgniter 4 Database Seeding](https://codeigniter.com/user_guide/dbmgmt/seeds.html)
- [CodeIgniter Shield Documentation](https://codeigniter4.github.io/shield/)

## Support

Jika mengalami masalah:

1. Cek log di `writable/logs/`
2. Periksa error message dengan teliti
3. Buka issue di GitHub repository
4. Tanya di forum komunitas CodeIgniter Indonesia

---

**Last Updated**: 2026-06-17
