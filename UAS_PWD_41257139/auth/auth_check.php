<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('kedai_cirebon_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function app_url(string $path = ''): string
{
    $appRoot = realpath(dirname(__DIR__));
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $basePath = '';

    if ($appRoot !== false && $documentRoot !== false) {
        $normalizedRoot = rtrim(str_replace('\\', '/', $appRoot), '/');
        $normalizedDocumentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');
        $documentPrefix = $normalizedDocumentRoot . '/';
        if (stripos($normalizedRoot, $documentPrefix) === 0) {
            $basePath = '/' . trim(substr($normalizedRoot, strlen($documentPrefix)), '/');
        }
    }

    return $basePath . '/' . ltrim($path, '/');
}

function csrf_token(): string
{
    return (string) $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals((string) $_SESSION['csrf_token'], $token);
}

function require_csrf(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!verify_csrf(is_string($token) ? $token : null)) {
        http_response_code(403);
        exit('Permintaan ditolak: token keamanan tidak valid. Muat ulang halaman dan coba lagi.');
    }
}

function require_admin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . app_url('auth/login.php'));
        exit;
    }
}

/**
 * Mengembalikan URL gambar produk yang tepat berdasarkan nama, kategori, dan gambar tersimpan.
 */
function product_image_url(string $nama, string $kategori, ?string $customImage = null): string
{
    $appRoot = realpath(dirname(__DIR__));
    $baseDir = $appRoot !== false ? str_replace('\\', '/', $appRoot) : str_replace('\\', '/', dirname(__DIR__));

    if ($customImage !== null && trim($customImage) !== '') {
        $customImage = trim($customImage);
        if (preg_match('#^https?://#i', $customImage) === 1) {
            return $customImage;
        }
        $relPath = ltrim(str_replace('\\', '/', $customImage), '/');
        if (file_exists($baseDir . '/' . $relPath)) {
            return app_url($relPath);
        }
    }

    $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $nama), '-'));
    $imgDir = $baseDir . '/assets/img/produk/';

    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        if (file_exists($imgDir . $slug . '.' . $ext)) {
            return app_url('assets/img/produk/' . $slug . '.' . $ext);
        }
    }

    $lowerName = strtolower($nama);
    if (str_contains($lowerName, 'pisang')) {
        return app_url('assets/img/produk/pisang-goreng-madu.jpg');
    }
    if (str_contains($lowerName, 'kayu manis') || str_contains($lowerName, 'cinnamon') || str_contains($lowerName, 'roti')) {
        return app_url('assets/img/produk/roti-kayu-manis.jpg');
    }
    if (str_contains($lowerName, 'pandan')) {
        return app_url('assets/img/produk/es-kopi-pandan.jpg');
    }
    if (str_contains($lowerName, 'aren') || str_contains($lowerName, 'susu') || str_contains($lowerName, 'latte')) {
        return app_url('assets/img/produk/kopi-susu-gula-aren.jpg');
    }
    if (str_contains($lowerName, 'robusta') || str_contains($lowerName, 'tubruk') || str_contains($lowerName, 'hitam') || str_contains($lowerName, 'espresso')) {
        return app_url('assets/img/produk/kopi-robusta-lereng.jpg');
    }
    if (str_contains($lowerName, 'arabika') || str_contains($lowerName, 'v60') || str_contains($lowerName, 'filter') || str_contains($lowerName, 'manual')) {
        return app_url('assets/img/produk/kopi-arabika-cirebon.jpg');
    }

    $cat = strtolower($kategori);
    if ($cat === 'kopi') {
        return app_url('assets/img/produk/kopi-default.jpg');
    }
    if ($cat === 'minuman') {
        return app_url('assets/img/produk/minuman-default.jpg');
    }
    if ($cat === 'kudapan') {
        return app_url('assets/img/produk/kudapan-default.jpg');
    }

    return app_url('assets/img/produk/kopi-default.jpg');
}
