<?php

declare(strict_types=1);

namespace WPROG2\S6_Databases\Ex6_3_Transactional_Database\public;

use PDO;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    http_response_code(400);
    exit;
}

$dsn = getenv('GUESTBOOK_DB_DSN');
$user = getenv('GUESTBOOK_DB_USER');
$password = getenv('GUESTBOOK_DB_PASSWORD');

if (!$dsn || !$user || !$password) {
    http_response_code(500);
    exit;
}

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