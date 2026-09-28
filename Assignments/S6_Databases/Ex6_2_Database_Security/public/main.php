<?php

namespace WPROG2\S6_Databases\Ex6_2_Database_Security\public;
use PDO;
use PDOException;

$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=guestbook_db;charset=utf8mb4';
$user = 'guestbook_user';
$password = 'guestbook_password';
$options = [
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $password, $options);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS entries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            website VARCHAR(255) NOT NULL,
            comment TEXT NOT NULL,
            created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $repository = new GuestbookRepository($pdo);

}
catch (PDOException $e) {
    error_log($e->getMessage());
}

