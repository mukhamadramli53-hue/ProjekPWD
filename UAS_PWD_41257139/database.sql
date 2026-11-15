-- Database: db_umkm_cirebon
-- Import melalui phpMyAdmin atau MySQL CLI sebelum menjalankan aplikasi.
CREATE DATABASE IF NOT EXISTS db_umkm_cirebon
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE db_umkm_cirebon;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS produk_umkm (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nama VARCHAR(120) NOT NULL,
    kategori ENUM('Kopi', 'Minuman', 'Kudapan') NOT NULL,
    deskripsi VARCHAR(1000) NOT NULL DEFAULT '',
    harga INT UNSIGNED NOT NULL,
    stok INT UNSIGNED NOT NULL DEFAULT 0,
    gambar VARCHAR(255) DEFAULT NULL,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_produk_nama_kategori (nama, kategori),
    KEY idx_produk_aktif_created (aktif, created_at),
    CONSTRAINT chk_produk_harga CHECK (harga > 0),
    CONSTRAINT chk_produk_stok CHECK (stok >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Akun demo: admin@kopi-cirebon.local / KopiCirebon!2026
-- Ganti kata sandi dan hapus akun demo sebelum deployment publik.
INSERT IGNORE INTO users (nama, email, password) VALUES
('Pengelola Kopi Cirebon', 'admin@kopi-cirebon.local', '$2y$10$JoPnLBTnXf2IpPCPwLCBXOCjDPuro2Oi8yNp9E.dr0xbpvXMWnDcK');

INSERT IGNORE INTO produk_umkm (nama, kategori, deskripsi, harga, stok, gambar, aktif) VALUES
('Kopi Arabika Cirebon', 'Kopi', 'Kopi arabika pilihan dengan aroma cokelat dan rasa seimbang.', 24000, 18, 'assets/img/produk/kopi-arabika-cirebon.jpg', 1),
('Kopi Susu Gula Aren', 'Minuman', 'Espresso, susu segar, dan gula aren lokal.', 22000, 14, 'assets/img/produk/kopi-susu-gula-aren.jpg', 1),
('Kopi Robusta Lereng', 'Kopi', 'Seduhan robusta bercita rasa kuat untuk teman produktif.', 18000, 10, 'assets/img/produk/kopi-robusta-lereng.jpg', 1),
('Es Kopi Pandan', 'Minuman', 'Kopi dingin dengan sentuhan pandan yang wangi.', 23000, 9, 'assets/img/produk/es-kopi-pandan.jpg', 1),
('Roti Kayu Manis', 'Kudapan', 'Roti rumahan lembut dengan taburan kayu manis.', 16000, 7, 'assets/img/produk/roti-kayu-manis.jpg', 1),
('Pisang Goreng Madu', 'Kudapan', 'Pisang pilihan digoreng renyah dengan madu.', 15000, 4, 'assets/img/produk/pisang-goreng-madu.jpg', 1);
