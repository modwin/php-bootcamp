<?php

namespace WPROG2\S8_Security\Ex8_5_Secure_Cookies\public;

use WPROG2\S2_User_Data\Ex2_1_UrlQueryParameters\public\HttpParamPrinter;

require_once __DIR__ . '/../../../../vendor/autoload.php';

header("Content-Type: text/plain; charset=utf-8");

$inputData = array_merge($_GET, $_POST, $_COOKIE);

HttpParamPrinter::printParams($inputData);