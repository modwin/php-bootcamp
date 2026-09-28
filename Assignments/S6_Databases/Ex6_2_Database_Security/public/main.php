<?php

namespace WPROG2\S6_Databases\Ex6_2_Database_Security\public;
use PDO;

$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=guestbook_db;charset=utf8mb4';
$user = 'guestbook_user';
$password = 'guestbook_password';
$options = [
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $user, $password, $options);
$repository = new GuestbookRepository($pdo);