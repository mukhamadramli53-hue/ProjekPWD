<?php
declare(strict_types=1);

ob_start();

require_once __DIR__ . '/auth/auth_check.php';
require_admin();

$routes = [
    'dashboard' => __DIR__ . '/modules/dashboard/index.php',
    'produk' => __DIR__ . '/modules/produk/list.php',
    'produk/tambah' => __DIR__ . '/modules/produk/tambah.php',
    'produk/impor' => __DIR__ . '/modules/produk/import.php',
];
$route = (string) ($_GET['route'] ?? 'dashboard');
if (!isset($routes[$route])) {
    http_response_code(404);
    $route = 'dashboard';
    $notFound = true;
} else {
    $notFound = false;
}

$pdo = require __DIR__ . '/config/database.php';
$pageTitle = $route === 'dashboard' ? 'Dashboard' : ($route === 'produk/tambah' ? 'Tambah Produk' : ($route === 'produk/impor' ? 'Impor Produk' : 'Katalog Produk'));
require __DIR__ . '/layouts/header.php';
?>
<!-- Argon 2 Signature Top Blue Gradient Background -->
<div class="absolute top-0 left-0 w-full min-h-72 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 z-0 pointer-events-none"></div>

<!-- Argon Dashboard Layout -->
<div class="relative min-h-screen">
    <!-- Floating Sidenav -->
    <?php require __DIR__ . '/layouts/sidebar.php'; ?>

    <!-- Main Content Container -->
    <div class="relative min-h-screen lg:ml-72 flex flex-col z-10 transition-all duration-200">
        <!-- Floating Glassmorphic Topbar -->
        <?php require __DIR__ . '/layouts/topbar.php'; ?>

        <!-- Main Body Content -->
        <main class="w-full px-6 py-4 mx-auto flex-1">
            <?php if ($notFound): ?>
                <div class="mb-5 rounded-2xl bg-amber-500/20 border border-amber-300 backdrop-blur-md p-4 text-white text-sm font-semibold flex items-center gap-3 shadow-md">
                    <i class="fa-solid fa-triangle-exclamation text-amber-200 text-lg"></i>
                    <span>Halaman yang diminta tidak ditemukan. Menampilkan ringkasan dashboard.</span>
                </div>
            <?php endif; ?>
            <?php require $routes[$route]; ?>
        </main>

        <!-- Footer -->
        <?php require __DIR__ . '/layouts/footer.php'; ?>
    </div>
</div>
