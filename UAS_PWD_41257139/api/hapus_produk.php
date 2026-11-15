<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth/auth_check.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sesi berakhir. Silakan masuk kembali.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['error' => 'Metode tidak diizinkan.']);
    exit;
}

$body = json_decode((string) file_get_contents('php://input'), true);
$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? (is_array($body) ? ($body['csrf_token'] ?? null) : null);
if (!verify_csrf(is_string($token) ? $token : null)) {
    http_response_code(403);
    echo json_encode(['error' => 'Token keamanan tidak valid. Muat ulang halaman.']);
    exit;
}
if (!is_array($body) || !isset($body['id']) || filter_var($body['id'], FILTER_VALIDATE_INT) === false || (int) $body['id'] < 1) {
    http_response_code(422);
    echo json_encode(['error' => 'ID produk tidak valid.']);
    exit;
}

$pdo = require __DIR__ . '/../config/database.php';
$statement = $pdo->prepare('DELETE FROM produk_umkm WHERE id = :id');
$statement->execute(['id' => (int) $body['id']]);
if ($statement->rowCount() === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Produk tidak ditemukan atau sudah dihapus.']);
    exit;
}

echo json_encode(['success' => true]);
