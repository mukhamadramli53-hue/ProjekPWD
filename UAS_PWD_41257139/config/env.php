<?php
declare(strict_types=1);

return [
    'DB_HOST' => getenv('DB_HOST') ?: '127.0.0.1',
    'DB_NAME' => getenv('DB_NAME') ?: 'db_umkm_cirebon',
    'DB_USER' => getenv('DB_USER') ?: 'root',
    'DB_PASS' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'APP_ENV' => getenv('APP_ENV') ?: 'development',
];
