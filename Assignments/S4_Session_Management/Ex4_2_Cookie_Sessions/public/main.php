<?php

namespace WPROG2\S4_Session_Management\Ex4_2_Cookie_Sessions\public;

require_once __DIR__ . '/../../../../vendor/autoload.php';

$sessionManager = new CookieSessionManager();
$sessionManager->getOrCreateSessionId();

header("Content-Type: text/html; charset=utf-8");
readfile(__DIR__ . '/sessions.html');