<?php
namespace WPROG2\S2_User_Data\Ex2_2_FormHandling;

require_once __DIR__ . '/../../../../vendor/autoload.php';

use WPROG2\S2_User_Data\Ex2_1_UrlQueryParameters\public\HttpParamPrinter;

header('Content-Type: text/plain; charset=utf-8');

HttpParamPrinter::printParams($_GET);
HttpParamPrinter::printParams($_POST);
