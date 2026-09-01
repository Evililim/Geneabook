<?php

declare(strict_types=1);

return [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => (int) (getenv('DB_PORT') ?: 5432),
    'database' => getenv('DB_DATABASE') ?: 'geneabook',
    'username' => getenv('DB_USERNAME') ?: 'geneabook',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => getenv('DB_CHARSET') ?: 'UTF8',
];
