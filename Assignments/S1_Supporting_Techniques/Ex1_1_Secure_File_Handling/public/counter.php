<?php
header('Content-type: text/html; charset=utf-8');

$file_name = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'counter.txt';
$file = "";
if(!file_exists($file_name)) {
    $file = fopen($file_name, 'w+');
}

$file = fopen($file_name, 'r+') or die("Error: Unable to open file.");
$count = fgets($file, 1000);
fclose($file);

$count = abs(intval($count)) + 1;

echo("<h1>Denna sida har besökts: $count gånger</h1>.");

$file = fopen($file_name, 'w');
fwrite($file, $count);
fclose($file);

