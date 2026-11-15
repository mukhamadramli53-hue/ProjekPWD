<?php
$productCount = (int) $pdo->query('SELECT COUNT(*) FROM produk_umkm')->fetchColumn();
?>
<section class="space-y-6">
    <?php if (isset($_GET['added'])): ?>
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs font-semibold text-emerald-800 flex items-center justify-between shadow-sm" role="status">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                <span>Produk baru berhasil ditambahkan ke katalog kedai!</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-sm cursor-pointer" title="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="rounded-2xl bg-blue-50 border border-blue-200 p-4 text-xs font-semibold text-blue-800 flex items-center justify-between shadow-sm" role="status">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-blue-500 text-base"></i>
                <span>Data produk berhasil diperbarui!</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-blue-600 hover:text-blue-800 text-sm cursor-pointer" title="Tutup">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- Main Argon Table Card -->
    <div class="relative flex flex-col min-w-0 break-words bg-white shadow-argon-xl rounded-2xl border-0 overflow-hidden">
        <!-- Card Header with Actions -->
        <div class="p-6 pb-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h3 class="text-base font-bold text-slate-800">Tabel Katalog Produk</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200/60"><?= number_format($productCount, 0, ',', '.') ?> item</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Kelola data menu, gambar, harga, dan persediaan stok secara langsung.</p>
            </div>
            
            <!-- Action Buttons in Argon Style -->
            <div class="flex items-center flex-wrap gap-2">
                <a href="<?= e(app_url('modules/produk/export.php')) ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm transition-all">
                    <i class="fa-solid fa-file-excel text-emerald-600"></i>
                    <span>Ekspor CSV</span>
                </a>
                <a href="<?= e(app_url('admin.php?route=produk/impor')) ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold uppercase tracking-wider text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm transition-all">
                    <i class="fa-solid fa-file-import text-purple-600"></i>
                    <span>Impor CSV</span>
                </a>
                <a href="<?= e(app_url('admin.php?route=produk/tambah')) ?>" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-tl from-blue-600 to-indigo-500 rounded-xl shadow-md shadow-blue-500/25 hover:scale-[1.02] active:scale-95 transition-all">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Produk</span>
                </a>
            </div>
        </div>

        <!-- Search Toolbar -->
        <div class="px-6 py-4 bg-slate-50/60 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
            <div class="relative w-full max-w-md text-slate-400">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                <input id="product-search" type="search" autocomplete="off" placeholder="Ketik nama kopi, kudapan, atau deskripsi…" class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-white border border-slate-200 text-slate-700 placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition-all">
            </div>
            <p id="search-hint" class="text-xs text-slate-400 hidden sm:block">Pencarian asinkron realtime via Fetch API.</p>
        </div>

        <!-- Table Container -->
        <div id="product-list" class="overflow-x-auto">
            <?php require __DIR__ . '/live_list.php'; ?>
        </div>
    </div>
</section>
