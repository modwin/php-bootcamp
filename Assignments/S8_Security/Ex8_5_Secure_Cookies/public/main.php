<?php
namespace WPROG2\S8_Security\Ex8_5_Secure_Cookies\public;
use WPROG2\S4_Session_Management\Ex4_2_Cookie_Sessions\public\CookieSessionManager;
require_once __DIR__ . '/../../../../vendor/autoload.php';

$sessionManager = new CookieSessionManager(3, "session_cookie", true);
$sessionId = $sessionManager->getOrCreateSessionId();

$html = file_get_contents(__DIR__ . '/example.html');
$html = str_replace('---session-id-secure---', htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'), $html);

header("Content-Type: text/html; charset=utf-8");
echo $html;
