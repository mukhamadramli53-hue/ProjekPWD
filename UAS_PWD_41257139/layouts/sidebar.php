<?php
$currentRoute = (string) ($_GET['route'] ?? 'dashboard');
?>
<!-- Mobile Backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden hidden transition-opacity duration-300"></div>

<!-- Argon Floating Sidenav -->
<aside id="sidenav-main" class="fixed inset-y-0 left-0 z-50 my-4 ml-4 w-64 -translate-x-[110%] lg:translate-x-0 rounded-2xl bg-white shadow-argon-xl flex flex-col justify-between transition-transform duration-300 ease-in-out border-0">
    <div>
        <!-- Brand Header -->
        <div class="h-20 flex items-center justify-between px-6">
            <a href="<?= e(app_url('admin.php')) ?>" class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-tl from-blue-600 to-indigo-500 text-white shadow-md">
                    <i class="fa-solid fa-mug-hot text-sm"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold tracking-tight text-slate-800">KOPI CIREBON</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Argon Dashboard</span>
                </div>
            </a>
            <button id="sidebar-close-btn" class="lg:hidden text-slate-400 hover:text-slate-600 p-1.5 rounded-lg text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <hr class="h-px mx-5 my-0 bg-transparent bg-gradient-to-r from-transparent via-slate-200 to-transparent">

        <!-- Navigation Links -->
        <div class="px-4 py-4 space-y-1">
            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Halaman Utama</p>

            <!-- Dashboard -->
            <a href="<?= e(app_url('admin.php?route=dashboard')) ?>" class="flex items-center gap-3 px-3.5 py-3 text-sm font-semibold rounded-xl transition-all <?= $currentRoute === 'dashboard' ? 'bg-blue-50/80 text-blue-600 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs shadow-sm <?= $currentRoute === 'dashboard' ? 'bg-gradient-to-tl from-blue-600 to-indigo-500 text-white shadow-blue-500/30' : 'bg-slate-100 text-slate-700' ?>">
                    <i class="fa-solid fa-tv"></i>
                </div>
                <span>Dashboard</span>
            </a>

            <!-- Katalog Produk -->
            <a href="<?= e(app_url('admin.php?route=produk')) ?>" class="flex items-center gap-3 px-3.5 py-3 text-sm font-semibold rounded-xl transition-all <?= str_starts_with($currentRoute, 'produk') && $currentRoute !== 'produk/tambah' && $currentRoute !== 'produk/impor' ? 'bg-blue-50/80 text-blue-600 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs shadow-sm <?= str_starts_with($currentRoute, 'produk') && $currentRoute !== 'produk/tambah' && $currentRoute !== 'produk/impor' ? 'bg-gradient-to-tl from-emerald-500 to-teal-400 text-white shadow-emerald-500/30' : 'bg-slate-100 text-slate-700' ?>">
                    <i class="fa-solid fa-table-list"></i>
                </div>
                <span>Katalog Produk</span>
            </a>

            <!-- Tambah Produk -->
            <a href="<?= e(app_url('admin.php?route=produk/tambah')) ?>" class="flex items-center gap-3 px-3.5 py-3 text-sm font-semibold rounded-xl transition-all <?= $currentRoute === 'produk/tambah' ? 'bg-blue-50/80 text-blue-600 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs shadow-sm <?= $currentRoute === 'produk/tambah' ? 'bg-gradient-to-tl from-orange-500 to-amber-400 text-white shadow-orange-500/30' : 'bg-slate-100 text-slate-700' ?>">
                    <i class="fa-solid fa-circle-plus"></i>
                </div>
                <span>Tambah Produk</span>
            </a>

            <!-- Impor CSV -->
            <a href="<?= e(app_url('admin.php?route=produk/impor')) ?>" class="flex items-center gap-3 px-3.5 py-3 text-sm font-semibold rounded-xl transition-all <?= $currentRoute === 'produk/impor' ? 'bg-blue-50/80 text-blue-600 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs shadow-sm <?= $currentRoute === 'produk/impor' ? 'bg-gradient-to-tl from-purple-600 to-pink-500 text-white shadow-purple-500/30' : 'bg-slate-100 text-slate-700' ?>">
                    <i class="fa-solid fa-file-csv"></i>
                </div>
                <span>Impor CSV</span>
            </a>

            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 pt-4 mb-2">Toko & Pengunjung</p>

            <!-- Ke Toko Publik -->
            <a href="<?= e(app_url()) ?>" target="_blank" class="flex items-center gap-3 px-3.5 py-3 text-sm font-semibold rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs shadow-sm bg-gradient-to-tl from-cyan-500 to-blue-400 text-white">
                    <i class="fa-solid fa-store"></i>
                </div>
                <span>Katalog Toko <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-1 opacity-70"></i></span>
            </a>
        </div>
    </div>

    <!-- Argon Card Box at Bottom of Sidenav -->
    <div class="mx-4 mb-4">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-tl from-blue-600 to-indigo-700 p-4 text-white shadow-lg">
            <div class="relative z-10">
                <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center mb-3">
                    <i class="fa-solid fa-circle-question text-white text-sm"></i>
                </div>
                <h6 class="font-bold text-sm text-white mb-0.5">Kedai Kopi Cirebon</h6>
                <p class="text-xs text-white/80 leading-relaxed mb-3">Sistem inventaris & pesanan kedai kopi lokal.</p>
                <a href="<?= e(app_url()) ?>" class="block w-full py-2 px-3 text-center text-xs font-bold uppercase tracking-wider text-slate-800 bg-white rounded-lg shadow-sm hover:bg-slate-50 transition-all">
                    Buka Toko ↗
                </a>
            </div>
            <!-- Decorative circle gradient background -->
            <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-white/10 blur-sm pointer-events-none"></div>
        </div>
    </div>
</aside>
