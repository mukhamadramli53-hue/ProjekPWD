<?php
declare(strict_types=1);

require_once __DIR__ . '/auth/auth_check.php';
$pdo = require __DIR__ . '/config/database.php';
$products = $pdo->query('SELECT id, nama, kategori, deskripsi, harga, stok, gambar FROM produk_umkm WHERE aktif = 1 ORDER BY created_at DESC')->fetchAll();
$categories = array_values(array_unique(array_column($products, 'kategori')));
?>
<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kopi pilihan dan kudapan UMKM lokal Cirebon.">
    <meta name="app-base" content="<?= e(app_url()) ?>">
    <title>Kopi Cirebon — Seduhan Lokal, Cerita Istimewa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Open Sans"', 'sans-serif'],
                    },
                    boxShadow: {
                        'argon': '0 7px 14px rgba(50,50,93,0.1), 0 3px 6px rgba(0,0,0,0.08)',
                        'argon-xl': '0 15px 35px rgba(50,50,93,0.1), 0 5px 15px rgba(0,0,0,0.07)',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= e(app_url('assets/css/app.css')) ?>">
</head>
<body class="m-0 font-sans antialiased bg-slate-50 text-slate-700">
    <!-- Modern Floating Header -->
    <header class="fixed inset-x-0 top-0 z-30 transition-all duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center justify-between px-6 py-3 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-white/10 shadow-argon-xl" aria-label="Navigasi utama">
                <a href="<?= e(app_url()) ?>" class="flex items-center gap-2.5 text-base font-bold text-white tracking-tight">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tl from-blue-600 to-indigo-500 text-white flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-mug-hot text-xs"></i>
                    </div>
                    <span>KOPI<span class="text-blue-400">.</span>CIREBON</span>
                </a>
                <div class="flex items-center gap-6 text-xs font-semibold text-white/90">
                    <a class="hidden hover:text-blue-300 sm:block transition-colors" href="#produk">Katalog Menu</a>
                    <a class="hidden hover:text-blue-300 sm:block transition-colors" href="#cerita">Tentang Kedai</a>
                    <a class="hidden hover:text-blue-300 sm:block transition-colors" href="#kalkulator">Estimasi Belanja</a>
                    <a href="<?= e(app_url('auth/login.php')) ?>" class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-tl from-blue-600 to-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-blue-500/25 hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="fa-solid fa-gauge-high text-xs"></i>
                        <span>Panel Admin</span>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main class="pt-0">
        <!-- Hero Section -->
        <section class="hero-slider relative flex min-h-[640px] items-center overflow-hidden bg-slate-950 text-white pt-20">
            <div class="hero-slide is-active" data-slide>
                <div class="mx-auto max-w-7xl px-6 py-32 lg:px-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-xs font-bold uppercase tracking-wider mb-6 backdrop-blur-sm">
                        <i class="fa-solid fa-coffee-bean text-xs"></i>
                        <span>Seduhan Khas Kota Udang</span>
                    </div>
                    <h1 class="max-w-3xl text-4xl sm:text-6xl font-black leading-tight tracking-tight">Satu teguk,<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300">seribu cerita istimewa.</span></h1>
                    <p class="mt-6 max-w-xl text-base sm:text-lg text-slate-300 leading-relaxed">Kopi lokal pilihan dan kudapan rumahan, diracik hangat oleh usaha kecil kebanggaan Cirebon.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="#produk" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-tl from-blue-600 to-indigo-600 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-blue-500/30 hover:scale-[1.02] active:scale-95 transition-all">
                            <span>Jelajahi Menu</span>
                            <i class="fa-solid fa-arrow-down text-xs"></i>
                        </a>
                        <a href="#cerita" class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-white hover:bg-white/20 transition-all">
                            <span>Cerita Kedai</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="hero-slide" data-slide aria-hidden="true">
                <div class="mx-auto max-w-7xl px-6 py-32 lg:px-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-6 backdrop-blur-sm">
                        <i class="fa-solid fa-seedling text-xs"></i>
                        <span>100% Petani Nusantara</span>
                    </div>
                    <h2 class="max-w-3xl text-4xl sm:text-6xl font-black leading-tight tracking-tight">Lokal rasanya,<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">hangat seduhannya.</span></h2>
                    <p class="mt-6 max-w-xl text-base sm:text-lg text-slate-300 leading-relaxed">Dukung ekosistem petani kopi dan perajin kudapan lokal lewat setiap tegukan yang kamu nikmati.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="#produk" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-tl from-emerald-500 to-teal-400 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-emerald-500/30 hover:scale-[1.02] active:scale-95 transition-all">
                            <span>Pesan Sekarang</span>
                            <i class="fa-solid fa-arrow-down text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
            <button type="button" class="slider-control slider-prev" aria-label="Slide sebelumnya">‹</button>
            <button type="button" class="slider-control slider-next" aria-label="Slide berikutnya">›</button>
            <div class="absolute bottom-8 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                <button class="slider-dot is-active" type="button" aria-label="Tampilkan slide 1"></button>
                <button class="slider-dot" type="button" aria-label="Tampilkan slide 2"></button>
            </div>
        </section>

        <!-- Cerita Kami Section -->
        <section id="cerita" class="mx-auto grid max-w-7xl gap-10 px-6 py-20 md:grid-cols-2 md:items-center lg:px-8">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-tl from-blue-600 via-indigo-600 to-blue-700 p-8 sm:p-12 text-white shadow-argon-xl">
                <div class="relative z-10">
                    <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider mb-4 backdrop-blur-sm">UMKM Kopi Cirebon</span>
                    <h3 class="text-2xl sm:text-3xl font-black leading-snug">Diracik pelan-pelan,<br>dinikmati bersama.</h3>
                    <p class="mt-4 text-xs sm:text-sm text-white/80 leading-relaxed">Kami mengangkat potensi biji kopi Robusta Lereng & Arabika Cirebon yang dipadukan dengan gula aren lokal dan aneka kudapan renyah khas buatan rumah.</p>
                </div>
                <div class="absolute -right-8 -bottom-8 w-40 h-40 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
            </div>
            <div class="space-y-4">
                <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-600">
                    <i class="fa-solid fa-heart text-rose-500"></i>
                    <span>Tentang Kedai Kami</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-800 tracking-tight">Rasa lokal autentik, dibuat dengan hati.</h2>
                <p class="text-sm leading-relaxed text-slate-500">Kopi Cirebon adalah inisiatif studi kasus UMKM kedai kopi lokal berbasis digital. Seluruh data katalog menu, stok persediaan, dan harga terkelola secara dinamis melalui dashboard admin.</p>
                <div class="flex items-center gap-3 pt-2 text-xs text-slate-400">
                    <i class="fa-solid fa-location-dot text-blue-600"></i>
                    <span>Lokasi: Kawasan Kesambi, Kota Cirebon, Jawa Barat</span>
                </div>
            </div>
        </section>

        <!-- Produk Section -->
        <section id="produk" class="bg-white py-20 border-y border-slate-100">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                    <div>
                        <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-600 mb-1">
                            <i class="fa-solid fa-mug-saucer"></i>
                            <span>Pilihan Menu Hari Ini</span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-800 tracking-tight">Katalog Menu Favorit</h2>
                    </div>
                    <p class="max-w-md text-xs sm:text-sm text-slate-500">Pilih menu favoritmu, tambahkan ke kalkulator pesanan, dan hitung estimasi total jajananmu.</p>
                </div>

                <!-- Category Filters in Argon Pill Style -->
                <div class="mt-8 flex flex-wrap gap-2.5" aria-label="Filter kategori produk">
                    <button class="gallery-filter is-selected rounded-xl px-4 py-2 text-xs font-bold uppercase tracking-wider border border-slate-200 transition-all cursor-pointer" data-category="all">Semua Menu</button>
                    <?php foreach ($categories as $category): ?>
                        <button class="gallery-filter rounded-xl px-4 py-2 text-xs font-bold uppercase tracking-wider border border-slate-200 text-slate-600 hover:bg-slate-100 transition-all cursor-pointer" data-category="<?= e($category) ?>"><?= e($category) ?></button>
                    <?php endforeach; ?>
                </div>

                <!-- Product Gallery Grid -->
                <div id="product-gallery" class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($products as $product): ?>
                        <?php $imgSrc = product_image_url($product['nama'], $product['kategori'], $product['gambar'] ?? null); ?>
                        <article class="product-card group relative flex flex-col min-w-0 break-words bg-white rounded-2xl shadow-argon-xl border-0 overflow-hidden hover:translate-y-[-4px] transition-all duration-300" data-category="<?= e($product['kategori']) ?>">
                            <div class="relative h-52 w-full overflow-hidden bg-slate-100">
                                <img src="<?= e($imgSrc) ?>" alt="<?= e($product['nama']) ?>" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" onerror="this.onerror=null; this.src='<?= e(app_url('assets/img/produk/kopi-default.jpg')) ?>';">
                                <span class="absolute left-3.5 top-3.5 inline-block px-3 py-1 rounded-lg text-xxs font-bold uppercase tracking-wider text-white bg-slate-900/80 backdrop-blur-sm shadow-sm"><?= e($product['kategori']) ?></span>
                            </div>
                            <div class="p-6 flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="text-base font-bold text-slate-800 leading-snug group-hover:text-blue-600 transition-colors"><?= e($product['nama']) ?></h3>
                                        <p class="whitespace-nowrap text-sm font-black text-slate-900">Rp <?= number_format((int) $product['harga'], 0, ',', '.') ?></p>
                                    </div>
                                    <p class="mt-2 text-xs leading-relaxed text-slate-500 min-h-[42px]"><?= e($product['deskripsi']) ?></p>
                                </div>
                                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                    <span class="text-xs text-slate-400 font-semibold flex items-center gap-1.5">
                                        <i class="fa-solid fa-box text-[10px]"></i>
                                        <span>Stok: <?= number_format((int) $product['stok'], 0, ',', '.') ?></span>
                                    </span>
                                    <button type="button" class="add-to-order inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-tl from-blue-600 to-indigo-600 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-blue-500/20 hover:scale-[1.03] active:scale-95 transition-all disabled:opacity-40 disabled:cursor-not-allowed" data-name="<?= e($product['nama']) ?>" data-price="<?= (int) $product['harga'] ?>" <?= (int) $product['stok'] < 1 ? 'disabled' : '' ?>>
                                        <i class="fa-solid fa-plus text-xs"></i>
                                        <span>Tambah</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    <?php if ($products === []): ?>
                        <div class="col-span-full rounded-2xl bg-slate-100 p-8 text-center text-slate-500 text-sm">
                            <i class="fa-solid fa-mug-hot text-3xl mb-2 text-slate-400 block"></i>
                            Menu sedang disiapkan. Silakan kunjungi kembali nanti.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Order Estimator Calculator Section -->
        <section id="kalkulator" class="mx-auto grid max-w-7xl gap-10 px-6 py-20 md:grid-cols-2 lg:px-8">
            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-600">
                    <i class="fa-solid fa-calculator"></i>
                    <span>Kalkulator Estimasi</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-800 tracking-tight">Rencanakan Jajanmu.</h2>
                <p class="text-sm leading-relaxed text-slate-500">Klik tombol <strong>+ Tambah</strong> pada menu di atas untuk menghitung simulasi perkiraan total pengeluaranmu. Fitur ini merupakan kalkulator estimasi publik dan tidak melakukan transaksi kartu atau uang riil.</p>
            </div>
            
            <!-- Argon Dark Order Card -->
            <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-7 text-white shadow-argon-xl border border-slate-800">
                <div class="flex items-center justify-between pb-4 border-b border-white/10 mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-basket-shopping text-blue-400"></i>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200">Daftar Pilihan</h3>
                    </div>
                    <span class="text-xs text-slate-400">Estimasi Belanja</span>
                </div>
                
                <ul id="order-items" class="space-y-3 text-xs text-slate-300 min-h-[80px]">
                    <li id="empty-order" class="text-slate-400 italic">Belum ada produk yang dipilih dari katalog.</li>
                </ul>

                <div class="mt-6 flex items-center justify-between border-t border-white/10 pt-5">
                    <span class="text-xs uppercase tracking-wider text-slate-400 font-bold">Perkiraan Total</span>
                    <output id="order-total" class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300" aria-live="polite">Rp 0</output>
                </div>
                
                <div class="mt-5 pt-3 border-t border-white/5 flex items-center justify-between">
                    <button id="clear-order" type="button" class="text-xs font-bold text-slate-400 hover:text-rose-400 transition-colors flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        <span>Kosongkan Pilihan</span>
                    </button>
                    <span class="text-[10px] text-slate-500">Harga sudah termasuk pajak</span>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 px-6 py-10 text-center text-xs text-slate-500 border-t border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-white font-bold text-sm">
                <i class="fa-solid fa-mug-hot text-blue-500"></i>
                <span>KOPI CIREBON</span>
            </div>
            <p>© <?= date('Y') ?> Kopi Cirebon · Studi kasus UMKM lokal untuk proyek UAS Pemrograman Web Dasar.</p>
            <a href="<?= e(app_url('auth/login.php')) ?>" class="text-slate-400 hover:text-white transition-colors">Admin Login →</a>
        </div>
    </footer>

    <script src="<?= e(app_url('assets/js/landing.js')) ?>" defer></script>
</body>
</html>
