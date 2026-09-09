<?php

namespace WPROG2\S4_Code_Separation\Ex4_1_HTML\public;
require_once __DIR__ . '/../../../../vendor/autoload.php';

use WPROG2\S2_User_Data\Ex2_1_UrlQueryParameters\public\HttpParamPrinter;
use Random\RandomException;

// 1. Determine or preserve Session ID
$sessionId = $_GET['session-id'] ?? $_POST['session-id'] ?? null;

if (!$sessionId) {
    try {
        $sessionId = bin2hex(random_bytes(16));
    } catch (RandomException $e) {
        http_response_code(500);
        echo "Error generating session ID.";
        return;
    }
}

// 2. If input was submitted (via GET link/form or POST form), output the parameters
if (isset($_GET['session-id']) || isset($_POST['session-id'])) {
    header("Content-Type: text/plain; charset=utf-8");
    $params = !empty($_POST) ? $_POST : $_GET;
    HttpParamPrinter::printParams($params);
    return; // Stop execution to prevent printing HTML
}

// 3. Initial page render (inject session ID into HTML template)
$templatePath = __DIR__ . '/sessions.html';
$html = file_get_contents($templatePath);

if ($html === false) {
    http_response_code(500);
    echo "Error: Unable to load template.";
    return;
}

$html = str_replace("---session-id---", $sessionId, $html);

header("Content-Type: text/html; charset=utf-8");
echo $html;









