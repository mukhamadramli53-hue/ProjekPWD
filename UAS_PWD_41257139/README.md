# Kopi Cirebon — Aplikasi UMKM

Proyek UAS Pemrograman Web Dasar: katalog publik dan dashboard admin untuk studi kasus kedai kopi UMKM di kawasan Kesambi, Kota Cirebon. Nama usaha dan data menu merupakan **studi kasus untuk tugas**, bukan klaim mewakili usaha tertentu.

## Fitur

- Landing page responsif dengan slider, filter galeri DOM, katalog produk, dan kalkulator estimasi pesanan.
- Login/registrasi pengelola berbasis sesi PHP; kata sandi diproses dengan `password_hash()` dan `password_verify()`.
- Dashboard ringkasan produk dan stok; daftar produk dengan pencarian langsung melalui Fetch API, tambah/hapus produk, serta notifikasi stok.
- Impor CSV tervalidasi (transaksi semua-atau-tidak-sama-sekali, batas 2 MB/500 baris) dan ekspor CSV yang mengurangi risiko formula injection.
- PDO prepared statements, output HTML `htmlspecialchars()`, token CSRF untuk seluruh form mutasi dan API hapus, serta Apache `.htaccess` untuk mencegah listing dan akses berkas sensitif.

## Menjalankan di Laragon

1. Pastikan PHP 8.1+ dengan ekstensi `pdo_mysql` dan `mbstring`, MySQL, serta Apache tersedia.
2. Salin folder proyek ke direktori web Laragon.
3. Buat basis data dan tabel dengan mengimpor `database.sql` melalui phpMyAdmin atau MySQL CLI.
4. Sesuaikan variabel lingkungan `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, dan `APP_ENV` pada konfigurasi virtual host/server. Pengembangan lokal menggunakan default `127.0.0.1`, `db_umkm_cirebon`, `root`, kata sandi kosong, dan `development`.
5. Buka URL lokal proyek melalui Laragon (baik virtual host proyek maupun subfolder `http://localhost/UAS_PWD_41257139/`). Apache wajib mengaktifkan `mod_rewrite`/`mod_headers` jika kebijakan server membutuhkannya; aturan di `.htaccess` berlaku pada Apache.
6. Masuk dengan akun demo `admin@kopi-cirebon.local` dan kata sandi `KopiCirebon!2026`, atau daftarkan akun pengelola.

> **Sebelum hosting publik:** ganti kredensial database melalui environment server, set `APP_ENV=production`, ganti/hapus akun demo, gunakan HTTPS, batasi pendaftaran pengelola (nonaktifkan `auth/registrasi.php` setelah membuat akun), atur izin tulis folder `logs/`, dan uji aturan Apache hosting. Kredensial produksi tidak disimpan di repositori.

## Format impor CSV

Unduh `assets/templates/produk_template.csv` dari halaman Impor CSV. Header harus persis dan berurutan:

```csv
nama,kategori,deskripsi,harga,stok,aktif
```

Kategori valid: `Kopi`, `Minuman`, `Kudapan`; `aktif` harus `0` atau `1`. Semua baris diperiksa sebelum transaksi database dimulai.

## Uji singkat

1. Buka landing page dan pastikan produk contoh tampil; uji slider, filter kategori, tombol tambah, dan kalkulator.
2. Masuk sebagai pengelola; coba cari produk, tambah produk, ekspor CSV, impor template, dan hapus salah satu produk.
3. Buka `/admin.php?route=produk/tambah` tanpa sesi (harus diarahkan ke login), lalu uji token CSRF yang tidak valid (harus ditolak).
4. Coba akses `/database.sql` dan `/config/env.php` melalui Apache (keduanya harus ditolak).

## Demo

URL video demo: **belum tersedia — rekam demo aplikasi setelah dijalankan secara lokal, unggah ke platform yang diminta dosen, lalu ganti baris ini dengan URL publik yang dapat diakses.**

Dokumen kebutuhan produk: [`PRD_Dokumen_Produk.pdf`](./PRD_Dokumen_Produk.pdf) (sumber teks: [`PRD_Dokumen_Produk.md`](./PRD_Dokumen_Produk.md)).
