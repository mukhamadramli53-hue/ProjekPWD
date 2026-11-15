<header class="relative flex flex-wrap items-center justify-between px-6 py-4 transition-all ease-in duration-200 z-20">
    <div class="flex items-center justify-between w-full flex-wrap gap-4">
        <!-- Breadcrumbs & Page Title -->
        <div>
            <nav aria-label="breadcrumb">
                <ol class="flex flex-wrap pt-1 bg-transparent rounded-lg text-xs">
                    <li class="leading-normal text-white/70">
                        <a href="<?= e(app_url('admin.php')) ?>" class="hover:text-white transition-colors">Admin</a>
                    </li>
                    <li class="text-white before:content-['/'] before:px-2 font-semibold">
                        <?= e($pageTitle) ?>
                    </li>
                </ol>
            </nav>
            <h1 class="mb-0 font-bold text-white text-xl sm:text-2xl tracking-tight capitalize mt-0.5"><?= e($pageTitle) ?></h1>
        </div>

        <!-- Right Controls: Search, Profile & Logout -->
        <div class="flex items-center gap-3">
            <!-- Search Widget (Links to product search if on product page) -->
            <div class="relative hidden sm:flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 text-xs pointer-events-none text-slate-400"></i>
                <input type="search" id="topbar-search" placeholder="Cari di menu..." class="pl-9 pr-4 py-2 text-xs rounded-xl bg-white/95 backdrop-blur-md border border-white/20 text-slate-700 placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-white/60 focus:bg-white w-48 lg:w-60 transition-all">
            </div>

            <!-- User Badge -->
            <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md text-white px-3.5 py-2 rounded-xl border border-white/20 text-xs font-semibold shadow-sm">
                <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-user"></i>
                </div>
                <span class="max-w-[120px] truncate hidden md:inline"><?= e($_SESSION['user_name'] ?? 'Pengelola') ?></span>
            </div>

            <!-- Logout Button -->
            <form method="post" action="<?= e(app_url('auth/logout.php')) ?>">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <button type="submit" title="Keluar" class="flex items-center gap-1.5 bg-white/15 hover:bg-white/25 text-white px-3.5 py-2 rounded-xl border border-white/20 text-xs font-bold transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>

            <!-- Mobile Hamburger Toggle -->
            <button id="sidebar-toggle-btn" type="button" class="lg:hidden flex items-center justify-center w-9 h-9 rounded-xl bg-white/15 text-white border border-white/20 hover:bg-white/25 transition-all" aria-label="Toggle Navigation">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>
        </div>
    </div>
</header>
