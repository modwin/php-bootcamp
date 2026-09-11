<?php

namespace WPROG2\S4_Session_Management\Ex4_2_Cookie_Sessions\public;

use WPROG2\S2_User_Data\Ex2_1_UrlQueryParameters\public\HttpParamPrinter;

require_once __DIR__ . '/../../../../vendor/autoload.php';

header("Content-Type: text/plain; charset=utf-8");

// Slå ihop alla inkommande parametrar och kakor så att HttpParamPrinter skriver ut dem
$inputData = array_merge($_GET, $_POST, $_COOKIE);

HttpParamPrinter::printParams($inputData);