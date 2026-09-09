<?php

namespace WPROG2\S3_Code_Separation\Ex3_1_Static\public;

require_once __DIR__ . '/../../../../vendor/autoload.php';

header("Content-Type: text/html; charset=utf-8");

$counter = new Counter("counter.txt");
$hits = $counter->incrementAndGet();

$html = file_get_contents("page.html");
$html = str_replace("{{hits}}", $hits, $html);
echo $html;
