# Panduan Troubleshooting Import Data Siswa

Dokumen ini berisi panduan lengkap untuk mengatasi masalah saat mengimport data siswa menggunakan file CSV.

## 📋 Daftar Isi

- [Persyaratan File CSV](#persyaratan-file-csv)
- [Langkah-langkah Import](#langkah-langkah-import)
- [Troubleshooting Umum](#troubleshooting-umum)
- [Cara Memeriksa Error](#cara-memeriksa-error)
- [Contoh File CSV yang Benar](#contoh-file-csv-yang-benar)

## 📝 Persyaratan File CSV

File CSV harus memenuhi kriteria berikut:

1. **Format Encoding**: UTF-8 (tanpa BOM)
2. **Delimiter**: Koma (,)
3. **Header Wajib**: Baris pertama harus berisi header kolom yang tepat
4. **Kolom Wajib**:
   - `nis` - Nomor Induk Siswa (angka)
   - `nama_siswa` - Nama lengkap siswa (teks)
   - `id_kelas` - ID kelas sesuai database (angka)
   - `jenis_kelamin` - "Laki-laki" atau "Perempuan"
   - `no_hp` - Nomor HP (angka, minimal 5 digit)

5. **Aturan Data**:
   - Jangan gunakan tanda kutip ganda (`"`) di dalam data
   - ID kelas harus sesuai dengan yang ada di database
   - NIS minimal 4 karakter
   - Nama siswa minimal 3 karakter

## 🚀 Langkah-langkah Import

### 1. Persiapan

1. Download template CSV dari halaman import
2. Lihat daftar ID kelas yang tersedia (klik "List Kelas")
3. Pastikan komputer/laptop terhubung internet

### 2. Isi Data CSV

Gunakan aplikasi spreadsheet (Excel, Google Sheets, LibreOffice Calc):

1. Buka template CSV yang sudah didownload
2. Isi data siswa sesuai kolom yang ada
3. Pastikan ID kelas sesuai dengan list kelas
4. **Penting**: Saat menyimpan, pilih:
   - Format: CSV (Comma delimited)
   - Encoding: UTF-8

### 3. Upload File

1. Buka halaman Import Siswa
2. Drag & drop file CSV ke area upload, atau klik tombol "Open the file Browser"
3. Tunggu proses import selesai
4. Periksa hasil import di daftar yang muncul

## 🔧 Troubleshooting Umum

### Problem 1: "File gagal diupload"

**Penyebab:**
- File bukan format CSV
- File corrupt atau tidak bisa dibaca
- Ukuran file terlalu besar

**Solusi:**
1. Pastikan file berekstensi `.csv`
2. Buka file dengan text editor untuk memastikan isinya valid
3. Coba buat file CSV baru dari template
4. Compress data jika terlalu banyak (upload bertahap)

### Problem 2: "Format file CSV tidak valid"

**Penyebab:**
- Header tidak sesuai
- Encoding bukan UTF-8
- Ada karakter khusus yang tidak didukung

**Solusi:**
1. Periksa baris pertama harus berisi: `nis,nama_siswa,id_kelas,jenis_kelamin,no_hp`
2. Simpan ulang dengan encoding UTF-8:
   - Excel: Save As → CSV UTF-8
   - Google Sheets: Download → CSV (.csv)
   - LibreOffice: Save As → Text CSV, Character set: Unicode (UTF-8)
3. Hapus karakter khusus seperti emoji atau simbol

### Problem 3: "File CSV tidak berisi data"

**Penyebab:**
- File hanya berisi header tanpa data
- Semua baris data kosong

**Solusi:**
1. Pastikan ada minimal 1 baris data setelah header
2. Periksa tidak ada baris yang benar-benar kosong
3. Hapus baris kosong di bagian akhir file

### Problem 4: "CSV missing required header"

**Penyebab:**
- Nama kolom header salah atau typo
- Urutan kolom tidak sesuai

**Solusi:**
1. Pastikan header **persis** seperti ini: `nis,nama_siswa,id_kelas,jenis_kelamin,no_hp`
2. Perhatikan:
   - Gunakan huruf kecil semua
   - Tidak ada spasi sebelum/sesudah nama kolom
   - Gunakan underscore (_) bukan spasi

### Problem 5: "Gagal mengimport data pada baris ke-X"

**Penyebab:**
- Data pada baris tersebut tidak lengkap
- ID kelas tidak ada di database
- NIS atau nama terlalu pendek
- Format data tidak sesuai

**Solusi:**
1. Periksa data di baris yang error
2. Pastikan semua kolom wajib terisi
3. Verifikasi ID kelas sesuai List Kelas
4. Pastikan NIS minimal 4 karakter, nama minimal 3 karakter
5. Pastikan no_hp hanya berisi angka

### Problem 6: "NIS ini telah terdaftar"

**Penyebab:**
- NIS sudah ada di database
- Ada duplikat NIS dalam file CSV

**Solusi:**
1. Periksa apakah siswa sudah pernah diinput sebelumnya
2. Cari duplikat NIS dalam file CSV
3. Gunakan NIS yang unik untuk setiap siswa

### Problem 7: Button Import tidak bisa diklik

**Penyebab:**
- JavaScript error di browser
- CSRF token expired
- Cache browser

**Solusi:**
1. Refresh halaman (F5 atau Ctrl+R)
2. Clear cache browser
3. Coba browser lain (Chrome, Firefox, Edge)
4. Logout dan login kembali
5. Periksa console browser untuk error (F12)

## 🔍 Cara Memeriksa Error

### Melihat Error di Browser Console

1. Buka browser console:
   - Chrome/Edge: Tekan F12 atau Ctrl+Shift+I
   - Firefox: Tekan F12 atau Ctrl+Shift+K
2. Pilih tab "Console"
3. Upload file CSV
4. Lihat pesan error yang muncul
5. Screenshot dan laporkan ke administrator jika perlu

### Melihat Error di Log Server

Jika Anda memiliki akses ke server:

1. Buka file log di `writable/logs/`
2. Cari file log terbaru (berdasarkan tanggal)
3. Cari kata kunci: "CSV", "import", "error"
4. Pesan error akan berisi detail masalah yang terjadi

## ✅ Contoh File CSV yang Benar

```csv
nis,nama_siswa,id_kelas,jenis_kelamin,no_hp
1234567,Ahmad Zaki,2,Laki-laki,85155092933
2345678,Siti Nurhaliza,2,Perempuan,85135092933
3456789,Budi Santoso,3,Laki-laki,85115092933
4567890,Dewi Lestari,3,Perempuan,85095092933
```

**Penjelasan:**
- Baris 1: Header dengan nama kolom yang tepat
- Baris 2-5: Data siswa
- Tidak ada baris kosong di tengah
- Tidak ada tanda kutip ganda
- ID kelas (2 dan 3) sesuai dengan database

## ❌ Contoh File CSV yang Salah

### Salah 1: Header tidak sesuai
```csv
NIS,NAMA,KELAS,GENDER,HP
1234567,Ahmad Zaki,2,L,85155092933
```
❌ Nama kolom harus lowercase dan sesuai template

### Salah 2: Ada tanda kutip ganda
```csv
nis,nama_siswa,id_kelas,jenis_kelamin,no_hp
1234567,"Ahmad ""Zaki""",2,Laki-laki,85155092933
```
❌ Jangan gunakan kutip ganda di dalam data

### Salah 3: Data tidak lengkap
```csv
nis,nama_siswa,id_kelas,jenis_kelamin,no_hp
1234567,Ahmad Zaki,,Laki-laki,85155092933
```
❌ id_kelas tidak boleh kosong

### Salah 4: Spasi di header
```csv
nis, nama_siswa, id_kelas, jenis_kelamin, no_hp
1234567,Ahmad Zaki,2,Laki-laki,85155092933
```
❌ Tidak boleh ada spasi setelah koma di header

## 📞 Bantuan Lebih Lanjut

Jika masih mengalami masalah setelah mengikuti panduan ini:

1. **Cek Console Browser**: Tekan F12, lihat tab Console untuk error JavaScript
2. **Cek Log Server**: Jika punya akses, periksa file log di `writable/logs/`
3. **Screenshot Error**: Ambil screenshot pesan error yang muncul
4. **Buat Issue**: Buat issue baru di GitHub dengan:
   - Deskripsi masalah
   - Screenshot error
   - Sample file CSV (tanpa data sensitif)
   - Versi PHP yang digunakan
   - Browser yang digunakan

## 🛠️ Tips Tambahan

1. **Backup Database**: Selalu backup database sebelum import besar
2. **Import Bertahap**: Untuk data banyak (>100 siswa), import dalam beberapa file kecil
3. **Validasi Data**: Periksa data di spreadsheet sebelum export ke CSV
4. **Test Dulu**: Upload file dengan 1-2 data untuk test dulu
5. **Gunakan Template**: Selalu gunakan template yang disediakan aplikasi

---

**Last Updated**: 2026-01-05
