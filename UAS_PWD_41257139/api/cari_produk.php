<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth/auth_check.php';
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Sesi berakhir. Silakan masuk kembali.']);
    exit;
}

$pdo = require __DIR__ . '/../config/database.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$query = trim((string) ($_GET['q'] ?? ''));
if (mb_strlen($query) > 100) {
    http_response_code(422);
    echo json_encode(['error' => 'Kata pencarian maksimal 100 karakter.']);
    exit;
}

$statement = $pdo->prepare(
    'SELECT id, nama, kategori, deskripsi, harga, stok, gambar, aktif
     FROM produk_umkm
     WHERE nama LIKE :nama OR kategori LIKE :kategori OR deskripsi LIKE :deskripsi
     ORDER BY created_at DESC LIMIT 200'
);
$term = '%' . $query . '%';
$statement->execute(['nama' => $term, 'kategori' => $term, 'deskripsi' => $term]);
$rawItems = $statement->fetchAll();
$items = array_map(static function (array $item): array {
    $item['image_url'] = product_image_url($item['nama'], $item['kategori'], $item['gambar'] ?? null);
    return $item;
}, $rawItems);
echo json_encode(['items' => $items], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
