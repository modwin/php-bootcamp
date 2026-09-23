<?php
namespace WPROG2\S4_Session_Management\Ex4_3_Native_Sessions\public;

use WPROG2\S2_User_Data\Ex2_1_UrlQueryParameters\public\HttpParamPrinter;

require_once __DIR__ . '/../../../../vendor/autoload.php';

// Starta/återuppta sessionen
session_start();

header("Content-Type: text/plain; charset=utf-8");

// Skriv ut alla inkommande POST/GET-parametrar
$params = !empty($_POST) ? $_POST : $_GET;

// Om testet kräver att sessions-ID syns i utskriften eller $_COOKIE:
HttpParamPrinter::printParams(array_merge($params, $_COOKIE));