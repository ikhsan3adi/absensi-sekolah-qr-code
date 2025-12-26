# Migration & Seeder Guide

Panduan lengkap untuk mengelola database migrations dan seeders pada aplikasi Absensi Sekolah QR Code.

## 📋 Daftar Isi

- [Tentang Migrations](#tentang-migrations)
- [Tentang Seeders](#tentang-seeders)
- [Urutan Migration](#urutan-migration)
- [Cara Penggunaan](#cara-penggunaan)
- [Struktur Database](#struktur-database)
- [Troubleshooting](#troubleshooting)

## 🗂️ Tentang Migrations

Migration adalah cara version control untuk database. File migration berada di `app/Database/Migrations/` dan dijalankan berurutan berdasarkan timestamp filename.

### Daftar Migration Files

1. **2023-08-18-000001_CreateJurusanTable.php**
   - Membuat tabel `tb_jurusan` (jurusan sekolah)
   - Kolom: id, jurusan, timestamps

2. **2023-08-18-000002_CreateKelasTable.php**
   - Membuat tabel `tb_kelas` (kelas)
   - Kolom: id_kelas, tingkat, id_jurusan, index_kelas, timestamps
   - Foreign key: id_jurusan → tb_jurusan(id)

3. **2023-08-18-000003_CreateDB.php**
   - Membuat 5 tabel utama:
     - `tb_kehadiran` - Master status kehadiran
     - `tb_guru` - Data guru
     - `tb_siswa` - Data siswa
     - `tb_presensi_guru` - Data presensi guru
     - `tb_presensi_siswa` - Data presensi siswa
   - Menambahkan foreign keys antar tabel

4. **2023-08-18-000004_AddSuperadmin.php**
   - Menambah kolom `is_superadmin` ke tabel `users` (Myth\Auth)

5. **2024-07-24-083011_GeneralSettings.php**
   - Membuat tabel `general_settings` untuk konfigurasi aplikasi
   - Kolom: id, logo, school_name, school_year, copyright

6. **2025-12-23-000001_AddRfidToSiswaGuru.php**
   - Menambah kolom `rfid_code` ke tabel `tb_siswa` dan `tb_guru`
   - Menambah index untuk performa pencarian

7. **2025-12-23-000002_AddWaliKelasToKelas.php**
   - Menambah kolom `id_wali_kelas` ke tabel `tb_kelas`
   - Menambah kolom `id_guru` ke tabel `users`
   - Foreign keys: id_wali_kelas → tb_guru(id_guru), id_guru → tb_guru(id_guru)

## 🌱 Tentang Seeders

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

6. **GeneralSettingsSeeder.php**
   - Mengisi pengaturan umum aplikasi
   - School Name: SMK 1 Indonesia
   - School Year: 2024/2025

7. **GuruSeeder.php** (Optional)
   - Contoh seeder untuk data guru (sudah ada)

8. **SiswaSeeder.php** (Optional)
   - Contoh seeder untuk data siswa (sudah ada)

## 📊 Urutan Migration

Migration akan dijalankan berdasarkan urutan timestamp berikut:

```
1. CreateJurusanTable        (tb_jurusan)
2. CreateKelasTable          (tb_kelas) → referensi tb_jurusan
3. CreateDB                  (5 tabel + foreign keys)
   - tb_kehadiran
   - tb_guru
   - tb_siswa            → referensi tb_kelas
   - tb_presensi_guru    → referensi tb_guru, tb_kehadiran
   - tb_presensi_siswa   → referensi tb_siswa, tb_kelas, tb_kehadiran
4. AddSuperadmin            (kolom is_superadmin ke users)
5. GeneralSettings          (general_settings)
6. AddRfidToSiswaGuru       (kolom rfid_code ke tb_siswa & tb_guru)
7. AddWaliKelasToKelas      (kolom id_wali_kelas ke tb_kelas, id_guru ke users)
```

## 🚀 Cara Penggunaan

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

## 🔧 Troubleshooting

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

**Penyebab**: Library Myth\Auth belum terinstall.

**Solusi**:

```bash
composer require myth/auth
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

## 📝 Best Practices

1. **Selalu backup database** sebelum menjalankan migration di production
2. **Test migration** di environment development terlebih dahulu
3. **Jangan edit migration yang sudah di-run** di production
4. **Buat migration baru** untuk perubahan schema
5. **Gunakan seeder** untuk data master, bukan untuk data transaksional
6. **Version control** semua migration dan seeder files
7. **Dokumentasikan perubahan** di CHANGELOG atau commit message

## 🔐 Keamanan

1. **Ubah password superadmin** setelah instalasi pertama:
   - Edit `app/Database/Seeds/SuperadminSeeder.php` sebelum seed
   - Atau ubah lewat aplikasi setelah login

2. **Jangan commit** file `.env` ke repository

3. **Gunakan environment variables** untuk credentials database

## 📚 Referensi

- [CodeIgniter 4 Migrations Documentation](https://codeigniter.com/user_guide/dbmgmt/migration.html)
- [CodeIgniter 4 Database Seeding](https://codeigniter.com/user_guide/dbmgmt/seeds.html)
- [Myth\Auth Library](https://github.com/lonnieezell/myth-auth)

## 📞 Support

Jika mengalami masalah:

1. Cek log di `writable/logs/`
2. Periksa error message dengan teliti
3. Buka issue di GitHub repository
4. Tanya di forum komunitas CodeIgniter Indonesia

---

**Last Updated**: 2025-12-26
