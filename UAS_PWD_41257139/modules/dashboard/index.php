<?php
$metrics = $pdo->query(
    'SELECT COUNT(*) AS total_produk, COALESCE(SUM(stok), 0) AS total_stok,
            COALESCE(SUM(CASE WHEN stok <= 5 THEN 1 ELSE 0 END), 0) AS stok_menipis
     FROM produk_umkm WHERE aktif = 1'
)->fetch();
$categories = $pdo->query(
    'SELECT kategori, COUNT(*) AS jumlah
     FROM produk_umkm WHERE aktif = 1 GROUP BY kategori ORDER BY jumlah DESC, kategori'
)->fetchAll();
$recent = $pdo->query(
    'SELECT nama, kategori, harga, stok, gambar FROM produk_umkm
     WHERE aktif = 1 ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

$totalItems = (int) $metrics['total_produk'];
?>
<section class="space-y-6">
    <!-- Argon Row 1: 4 Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Card 1: Total Produk -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-argon-xl rounded-2xl p-5 border-0 hover:translate-y-[-2px] transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Total Produk</p>
                    <h4 class="text-2xl font-bold text-slate-700 tracking-tight mb-1"><?= number_format($totalItems, 0, ',', '.') ?></h4>
                    <p class="text-xs text-slate-500 mb-0 flex items-center gap-1">
                        <span class="font-bold text-emerald-500 flex items-center"><i class="fa-solid fa-arrow-up text-[10px] mr-1"></i>100%</span>
                        <span>katalog aktif</span>
                    </p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tl from-blue-600 to-indigo-500 text-white shadow-md shadow-blue-500/30">
                    <i class="fa-solid fa-mug-saucer text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Stok -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-argon-xl rounded-2xl p-5 border-0 hover:translate-y-[-2px] transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Total Stok</p>
                    <h4 class="text-2xl font-bold text-slate-700 tracking-tight mb-1"><?= number_format((int) $metrics['total_stok'], 0, ',', '.') ?></h4>
                    <p class="text-xs text-slate-500 mb-0 flex items-center gap-1">
                        <span class="font-bold text-emerald-500">Unit</span>
                        <span>siap disajikan</span>
                    </p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tl from-emerald-500 to-teal-400 text-white shadow-md shadow-emerald-500/30">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Perlu Restok (<= 5) -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-argon-xl rounded-2xl p-5 border-0 hover:translate-y-[-2px] transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Perlu Restok</p>
                    <h4 class="text-2xl font-bold text-slate-700 tracking-tight mb-1"><?= number_format((int) $metrics['stok_menipis'], 0, ',', '.') ?></h4>
                    <p class="text-xs text-slate-500 mb-0 flex items-center gap-1">
                        <?php if ((int) $metrics['stok_menipis'] > 0): ?>
                            <span class="font-bold text-rose-500 flex items-center"><i class="fa-solid fa-triangle-exclamation text-[10px] mr-1"></i>Stok &le; 5</span>
                        <?php else: ?>
                            <span class="font-bold text-emerald-500">Aman</span>
                        <?php endif; ?>
                        <span>varian menipis</span>
                    </p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tl from-rose-500 to-red-400 text-white shadow-md shadow-rose-500/30">
                    <i class="fa-solid fa-bell text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Kategori Menu -->
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-argon-xl rounded-2xl p-5 border-0 hover:translate-y-[-2px] transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Kategori</p>
                    <h4 class="text-2xl font-bold text-slate-700 tracking-tight mb-1"><?= count($categories) ?></h4>
                    <p class="text-xs text-slate-500 mb-0 flex items-center gap-1">
                        <span class="font-bold text-purple-600">Lengkap</span>
                        <span>Kopi, Minum, Camilan</span>
                    </p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tl from-purple-600 to-indigo-500 text-white shadow-md shadow-purple-500/30">
                    <i class="fa-solid fa-layer-group text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Argon Row 2: Recent Products Table + Category Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Recent Products Table (Col-8) -->
        <div class="lg:col-span-8 relative flex flex-col min-w-0 break-words bg-white shadow-argon-xl rounded-2xl border-0 overflow-hidden">
            <!-- Card Header -->
            <div class="p-6 pb-4 flex items-center justify-between border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Produk Terbaru</h3>
                    <p class="text-xs text-slate-400">Daftar produk yang baru ditambahkan ke katalog kedai.</p>
                </div>
                <a href="<?= e(app_url('admin.php?route=produk')) ?>" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold uppercase tracking-wider text-blue-600 bg-blue-50/70 hover:bg-blue-100 rounded-xl transition-all">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/60">
                        <tr>
                            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider text-center">Stok</th>
                            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($recent as $product): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="<?= e(product_image_url($product['nama'], $product['kategori'], $product['gambar'] ?? null)) ?>" alt="<?= e($product['nama']) ?>" class="h-11 w-11 rounded-xl object-cover shadow-sm border border-slate-100 shrink-0" onerror="this.onerror=null; this.src='<?= e(app_url('assets/img/produk/kopi-default.jpg')) ?>';">
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 leading-tight mb-0.5"><?= e($product['nama']) ?></p>
                                            <span class="text-xs text-slate-400">ID: #<?= (int) ($product['id'] ?? 0) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-1 text-xxs font-bold rounded-lg uppercase tracking-wider <?= $product['kategori'] === 'Kopi' ? 'bg-amber-50 text-amber-700 border border-amber-200' : ($product['kategori'] === 'Minuman' ? 'bg-cyan-50 text-cyan-700 border border-cyan-200' : 'bg-orange-50 text-orange-700 border border-orange-200') ?>">
                                        <?= e($product['kategori']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-slate-700">Rp <?= number_format((int) $product['harga'], 0, ',', '.') ?></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full <?= (int) $product['stok'] <= 5 ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-slate-100 text-slate-700' ?>">
                                        <?= number_format((int) $product['stok'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xxs font-bold rounded-md bg-gradient-to-tl from-emerald-500 to-teal-400 text-white shadow-sm">
                                        <i class="fa-solid fa-check text-[9px]"></i> Aktif
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($recent === []): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-400">Belum ada data produk. Silakan tambahkan menu pertama.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Category Breakdown & Quick Actions (Col-4) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Category Distribution Card -->
            <div class="relative flex flex-col min-w-0 break-words bg-white shadow-argon-xl rounded-2xl border-0 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800">Distribusi Menu</h3>
                    <i class="fa-solid fa-chart-pie text-slate-400 text-sm"></i>
                </div>
                <div class="space-y-4">
                    <?php 
                    $categoryColors = [
                        'Kopi' => ['gradient' => 'from-blue-600 to-indigo-500', 'text' => 'text-blue-600', 'icon' => 'fa-mug-hot'],
                        'Minuman' => ['gradient' => 'from-cyan-500 to-teal-400', 'text' => 'text-cyan-600', 'icon' => 'fa-glass-water'],
                        'Kudapan' => ['gradient' => 'from-orange-500 to-amber-400', 'text' => 'text-orange-600', 'icon' => 'fa-cookie-bite'],
                    ];
                    foreach ($categories as $cat): 
                        $percent = $totalItems > 0 ? round(((int) $cat['jumlah'] / $totalItems) * 100) : 0;
                        $scheme = $categoryColors[$cat['kategori']] ?? ['gradient' => 'from-purple-600 to-indigo-500', 'text' => 'text-purple-600', 'icon' => 'fa-tag'];
                    ?>
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                                <span class="flex items-center gap-2 text-slate-700">
                                    <i class="fa-solid <?= $scheme['icon'] ?> <?= $scheme['text'] ?> text-xs"></i>
                                    <span><?= e($cat['kategori']) ?></span>
                                </span>
                                <span class="text-slate-500 font-bold"><?= number_format((int) $cat['jumlah'], 0, ',', '.') ?> (<?= $percent ?>%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r <?= $scheme['gradient'] ?> h-2 rounded-full" style="width: <?= $percent ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($categories === []): ?>
                        <p class="text-xs text-slate-400">Data kategori belum tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="relative flex flex-col min-w-0 break-words bg-white shadow-argon-xl rounded-2xl border-0 p-6">
                <h3 class="text-base font-bold text-slate-800 mb-3">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="<?= e(app_url('admin.php?route=produk/tambah')) ?>" class="flex flex-col items-center justify-center p-3.5 rounded-xl bg-slate-50 hover:bg-blue-50/70 border border-slate-100 hover:border-blue-200 transition-all text-center group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tl from-blue-600 to-indigo-500 text-white flex items-center justify-center mb-2 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600">Tambah Menu</span>
                    </a>
                    <a href="<?= e(app_url('admin.php?route=produk/impor')) ?>" class="flex flex-col items-center justify-center p-3.5 rounded-xl bg-slate-50 hover:bg-purple-50/70 border border-slate-100 hover:border-purple-200 transition-all text-center group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tl from-purple-600 to-pink-500 text-white flex items-center justify-center mb-2 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-file-import text-sm"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700 group-hover:text-purple-600">Impor CSV</span>
                    </a>
                    <a href="<?= e(app_url('modules/produk/export.php')) ?>" class="flex flex-col items-center justify-center p-3.5 rounded-xl bg-slate-50 hover:bg-emerald-50/70 border border-slate-100 hover:border-emerald-200 transition-all text-center group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tl from-emerald-500 to-teal-400 text-white flex items-center justify-center mb-2 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-file-excel text-sm"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700 group-hover:text-emerald-600">Ekspor CSV</span>
                    </a>
                    <a href="<?= e(app_url()) ?>" target="_blank" class="flex flex-col items-center justify-center p-3.5 rounded-xl bg-slate-50 hover:bg-cyan-50/70 border border-slate-100 hover:border-cyan-200 transition-all text-center group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tl from-cyan-500 to-blue-400 text-white flex items-center justify-center mb-2 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700 group-hover:text-cyan-600">Buka Toko</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
