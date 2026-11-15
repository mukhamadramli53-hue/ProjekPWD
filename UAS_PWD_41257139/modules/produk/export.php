<?php
declare(strict_types=1);

require_once __DIR__ . '/../../auth/auth_check.php';
require_admin();
$pdo = require __DIR__ . '/../../config/database.php';

$filename = 'produk-kopi-cirebon-' . date('Ymd-His') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('X-Content-Type-Options: nosniff');

$output = fopen('php://output', 'wb');
if ($output === false) {
    throw new RuntimeException('Tidak dapat membuka aliran ekspor CSV.');
}
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['nama', 'kategori', 'deskripsi', 'harga', 'stok', 'aktif']);

$statement = $pdo->query('SELECT nama, kategori, deskripsi, harga, stok, aktif FROM produk_umkm ORDER BY id');
while ($row = $statement->fetch(PDO::FETCH_NUM)) {
    $safeRow = array_map(static function (mixed $value): string {
        $cell = (string) $value;
        if (preg_match('/^[\s]*[=+\-@]/u', $cell) === 1) {
            return "'" . $cell;
        }
        return $cell;
    }, $row);
    fputcsv($output, $safeRow);
}
fclose($output);
