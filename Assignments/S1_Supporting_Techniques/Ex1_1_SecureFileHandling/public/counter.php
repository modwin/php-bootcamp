<?php
header('Content-type: text/plain; charset=utf-8');

$file_name = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'counter.txt';
$file = "";
if(!file_exists($file_name)) {
    $file = fopen($file_name, 'w+');
}

$file = fopen($file_name, 'r+') or die("Error: Unable to open file.");
$count = fgets($file, 1000);
fclose($file);

$count = abs(intval($count)) + 1;

echo("$count");

$file = fopen($file_name, 'w');
if(flock($file, LOCK_EX)) {
    fwrite($file, $count);
    fflush($file);
}

fclose($file);

