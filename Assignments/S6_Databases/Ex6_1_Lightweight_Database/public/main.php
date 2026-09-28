<?php

require_once __DIR__ . '/VisitLog.php';
require_once __DIR__ . '/VisitLogRepository.php';

use WPROG2\S6_Databases\Ex6_1_Lightweight_Database\public\VisitLog;
use WPROG2\S6_Databases\Ex6_1_Lightweight_Database\public\VisitLogRepository;

/*$dbPath = __DIR__ . '/visits.sqlite';*/

$pdo = new PDO('sqlite:' . __DIR__ . '/visits.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS visits (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        visited_at TEXT NOT NULL,
        remote_address TEXT NOT NULL,
        user_agent TEXT NOT NULL
    )'
);

$repository = new VisitLogRepository($pdo);

$visit = new VisitLog(
    date('Y-m-d H:i:s'),
    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
);

$repository->addVisit($visit);

foreach ($repository->findAll() as $visitLog) {
    echo $visitLog . "\n";
}