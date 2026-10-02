<?php


declare(strict_types=1);

$dsn = getenv('WPROG2_DB_DSN') ?: getenv('DB_DSN') ?: 'mysql:host=127.0.0.1;port=3306;dbname=guestbook_db;charset=utf8mb4';
$user = getenv('WPROG2_DB_USER') ?: getenv('DB_USER') ?: 'guestbook_user';
$password = getenv('WPROG2_DB_PASSWORD') ?: getenv('DB_PASSWORD') ?: '';

return [
    'document_root' => 'public',
    'routes' => [
        'main' => '/main.php',
        'form' => '/main.php',
        'image' => '/image.php',
    ],
    'environment' => [
        'GUESTBOOK_DB_DSN' => $dsn,
        'GUESTBOOK_DB_USER' => $user,
        'GUESTBOOK_DB_PASSWORD' => $password,
    ],
    'services' => ['database'],
    'features' => [],
    'settings' => [
        'entry_table' => 'entries',
        'fields' => [
            'name' => 'name',
            'email' => 'email',
            'website' => 'homepage',
            'comment' => 'comment',
            'image' => 'filename'
        ],
    ],
];
