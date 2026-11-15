# Product Requirement Document
## Aplikasi UMKM — Kopi Cirebon

**Jenis dokumen:** PRD dan analisis kebutuhan  
**Mata kuliah:** Pemrograman Web Dasar  
**Studi kasus:** Kedai kopi lokal di kawasan Kesambi, Kota Cirebon  
**Status:** Implementasi tugas UAS  
**Catatan:** Nama usaha, alamat, menu, dan data di dokumen ini adalah skenario studi kasus; validasikan dan ganti jika menggunakan mitra UMKM nyata.

## 1. Ringkasan produk

Kopi Cirebon adalah aplikasi web sederhana untuk memperkenalkan katalog kedai kopi lokal kepada calon konsumen dan membantu pengelola memelihara data produk serta stok. Aplikasi memiliki dua antarmuka yang terintegrasi: landing page publik dan dashboard admin yang memerlukan sesi terautentikasi.

## 2. Masalah dan tujuan

UMKM membutuhkan katalog yang mudah dibuka dari perangkat seluler, sementara perubahan harga dan stok sebaiknya dapat dilakukan tanpa mengubah kode halaman. Produk ini menyediakan tampilan publik dan pengelolaan katalog dalam satu aplikasi PHP/MySQL.

Tujuan:
- Menyajikan identitas usaha dan produk secara ringkas dan responsif.
- Memungkinkan pengelola mendaftarkan/mengautentikasi akun dan melihat ringkasan produk/stok.
- Mengelola produk dengan validasi, pencarian asinkron, serta pertukaran data CSV.
- Menerapkan praktik dasar keamanan sebelum aplikasi dipasang pada hosting Apache.

## 3. Pengguna dan kebutuhan

| Pengguna | Kebutuhan |
|---|---|
| Calon konsumen | Melihat cerita usaha, kategori menu, deskripsi, harga, dan stok. Menghitung perkiraan belanja tanpa proses transaksi. |
| Pengelola kedai | Masuk secara aman, melihat metrik, menambah dan menghapus produk, mencari katalog, serta mengimpor/mengekspor CSV. |
| Pemilik/developer | Mengatur kredensial lewat environment, mengimpor skema MySQL, dan memasang aplikasi dengan aturan Apache yang aman. |

## 4. Ruang lingkup

**Termasuk:** katalog publik; slider promosi; filter kategori; kalkulator estimasi; registrasi/login/logout; sesi dan CSRF; dashboard inventaris; tambah/hapus produk; live search Fetch API; impor/ekspor CSV; konfigurasi PDO; SQL schema dan data awal; hardening Apache; dokumentasi dan PRD.

**Tidak termasuk:** pembayaran, keranjang yang disimpan, pemesanan online, pengiriman, manajemen multi-peran, edit produk, laporan penjualan, dan integrasi WhatsApp. Kalkulator publik hanya estimasi dan tidak mengirim transaksi.

## 5. Kebutuhan fungsional

| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| FR-01 | Pengunjung melihat katalog produk aktif | Nama, kategori, deskripsi, harga dan stok ditampilkan; teks database di-escape. |
| FR-02 | Pengunjung memilih kategori dan menghitung estimasi | Filter DOM menampilkan kategori terpilih; total berubah ketika item ditambah/dikosongkan. |
| FR-03 | Pengelola mendaftar dan masuk | Email unik; password minimal 10 karakter; hash diverifikasi; ID sesi diregenerasi setelah autentikasi. |
| FR-04 | Dashboard merangkum inventaris | Menampilkan jumlah produk, jumlah stok dan produk dengan stok ≤ 5. |
| FR-05 | Pengelola menambah produk | Nama, kategori, harga dan stok tervalidasi; form POST memuat token CSRF. |
| FR-06 | Pengelola mencari katalog | Fetch API mengembalikan produk menurut nama/kategori/deskripsi tanpa memuat ulang halaman. |
| FR-07 | Pengelola menghapus produk | Hanya POST terautentikasi dengan CSRF; aksi dikonfirmasi dan respons JSON ditampilkan. |
| FR-08 | Pengelola mengimpor CSV | Header dan data tervalidasi, batas 2 MB/500 baris, semua data disimpan dalam satu transaksi. |
| FR-09 | Pengelola mengekspor CSV | Mengunduh kolom katalog dan melindungi spreadsheet dari nilai formula berawalan karakter berbahaya. |

## 6. Kebutuhan nonfungsional dan keamanan

- PHP 8.1+; MySQL 8+; PDO dengan exception mode, prepared statements dan `utf8mb4`.
- Semua keluaran dinamis HTML menggunakan `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')`.
- Token CSRF acak per sesi diverifikasi memakai `hash_equals()` pada form mutasi dan endpoint penghapusan.
- Password disimpan sebagai hash adaptif; sesi memakai cookie HttpOnly dan SameSite serta diregenerasi saat autentikasi.
- `.htaccess` mematikan directory listing dan menolak akses SQL, konfigurasi, log, backup, serta berkas tersembunyi.
- `APP_ENV=production` mematikan `display_errors` dan mengarahkan galat ke `logs/error.log`.
- Kredensial deployment disediakan melalui environment server, tidak disimpan sebagai rahasia dalam repositori.
- Antarmuka responsif, navigasi utama dapat digunakan dengan keyboard, dan pembaruan pencarian memiliki status aria-live.

## 7. Data dan model

**users**: `id` (PK), `nama`, `email` (unik), `password` (hash), `created_at`.  
**produk_umkm**: `id` (PK), `nama`, `kategori`, `deskripsi`, `harga`, `stok`, `aktif`, `created_at`, `updated_at`.  
Relasi: setiap pengelola dapat mengakses katalog usaha yang sama; skenario ini belum memiliki pemisahan kepemilikan atau multi-tenant.

## 8. Alur utama

1. Pengunjung membuka landing page; server mengambil produk aktif dari MySQL, lalu browser merender galeri dan kalkulator.
2. Pengelola mendaftar atau masuk; server memverifikasi password, meregenerasi sesi, dan mengarahkan ke dashboard.
3. Pengelola mengirim form tambah dengan token CSRF; server memvalidasi data dan menyimpan lewat prepared statement.
4. Saat pencarian, browser meminta JSON ke endpoint; hasil dirender sebagai teks DOM agar konten tidak ditafsirkan sebagai HTML.
5. Saat hapus, browser meminta konfirmasi dan mengirim POST dengan token CSRF; server memeriksa sesi, token, dan ID sebelum menghapus.
6. Impor CSV memvalidasi header dan seluruh baris terlebih dahulu, lalu menyimpan semuanya dalam transaksi; ekspor mengirim CSV dengan penanganan formula spreadsheet.

## 9. Arsitektur dan struktur

- `index.php`: landing page publik.
- `admin.php`: controller dashboard dengan whitelist route.
- `config/`: environment dan koneksi PDO.
- `auth/`: helper sesi/CSRF serta login, registrasi, logout.
- `layouts/`: template navigasi dan kerangka dashboard.
- `modules/dashboard/`, `modules/produk/`: modul ringkasan, katalog, tambah, impor, ekspor.
- `api/`: pencarian JSON dan penghapusan asinkron.
- `assets/`: CSS, JavaScript, dan template CSV.
- `database.sql`: pembuatan tabel dan data contoh.

## 10. Uji penerimaan

| Kasus | Hasil yang diharapkan |
|---|---|
| Membuka landing page setelah import SQL | Produk aktif dan nilai rupiah tampil tanpa galat. |
| Menambah produk dengan harga/stok negatif | Validasi menolak data; database tidak berubah. |
| Mengirim POST tanpa token CSRF | Server menolak dengan HTTP 403. |
| Membuka dashboard tanpa login | Pengunjung diarahkan ke form masuk. |
| Mencari kata yang cocok/tidak cocok | Baris cocok tampil; pencarian kosong memberi pesan yang jelas. |
| Mengimpor CSV salah header atau salah satu baris | Seluruh impor dibatalkan dan kesalahan dilaporkan. |
| Menghapus produk via GET atau token salah | Metode tidak diizinkan atau HTTP 403; data tidak berubah. |
| Mengakses database/config lewat Apache | Server menolak akses langsung. |

## 11. Deployment dan operasional

Impor `database.sql`, set environment database pada hosting, atur `APP_ENV=production`, aktifkan HTTPS, pastikan direktori log dapat ditulis hanya oleh aplikasi, ganti akun dan password demo, lalu nonaktifkan registrasi publik setelah akun operasional dibuat. Uji aturan `.htaccess` di Apache target karena server selain Apache mungkin memerlukan konfigurasi ekuivalen.

## 12. Risiko dan batasan

- Data usaha dan menu saat ini contoh studi kasus; minta izin dan verifikasi informasi sebelum mewakili UMKM nyata.
- Kredensial demo hanya untuk pengujian lokal dan wajib diganti sebelum deployment.
- Form registrasi terbuka untuk kebutuhan demonstrasi; produksi memerlukan pembatasan pendaftaran/undangan.
- Harga serta stok bukan bukti ketersediaan transaksi karena aplikasi belum memiliki sistem order.
- Tailwind CDN memerlukan koneksi internet; hosting produksi dapat membangun CSS lokal untuk mengurangi ketergantungan.

## 13. Hasil yang diserahkan

Aplikasi PHP modular, skema dan data awal MySQL, konfigurasi hardening Apache, panduan setup pada `README.md`, serta dokumen PRD PDF ini. URL video demo harus ditambahkan setelah video direkam dan diunggah.
