<?php
declare(strict_types=1);

$config = require __DIR__ . '/env.php';

if ($config['APP_ENV'] === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/error.log');
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=utf8mb4',
    $config['DB_HOST'],
    $config['DB_NAME']
);

$pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

return $pdo;
