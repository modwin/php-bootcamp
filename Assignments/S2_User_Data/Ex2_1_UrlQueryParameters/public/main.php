<?php
namespace WPROG2\S2_User_Data\Ex2_1_UrlQueryParameters\public;

require_once __DIR__ . '/HttpParamPrinter.php';

header('Content-Type: text/plain; charset=utf-8');

HttpParamPrinter::printParams($_GET);