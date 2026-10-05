<?php

declare(strict_types=1);

namespace WPROG2\S6_Databases\Ex6_3_Transactional_Database\public;

use PDO;
use PDOException;

require_once __DIR__ . '/EnvSetter.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id <= 0) {
    http_response_code(400);
    exit;
}

$envSetter = new EnvSetter();
$envSetter->load(__DIR__ . '/../.env', overwrite: true);

$dsn = getenv('GUESTBOOK_DB_DSN');
$user = getenv('GUESTBOOK_DB_USER');
$password = getenv('GUESTBOOK_DB_PASSWORD');

if (!$dsn || !$user || !$password) {
    http_response_code(500);
    echo 'The guestbook database environment is incomplete.';
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $stmt = $pdo->prepare('SELECT mime_type, image_data FROM images WHERE entry_id = :id');
    $stmt->execute([':id' => $id]);
    $image = $stmt->fetch();

    if ($image === false) {
        http_response_code(404);
        exit;
    }

    header('Content-Type: ' . $image['mime_type']);
    echo $image['image_data'];
} catch (PDOException $e) {
    http_response_code(500);
    exit;
}