<?php

namespace WPROG2\S2_User_Data\Ex2_3_FileUpload\public;

require_once __DIR__ . '/../../../../vendor/autoload.php';
header("Content-Type: text/plain; charset=utf-8");
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);


if (!empty($_FILES)){
    $current_file = reset($_FILES);
    FileHandler::process($current_file);
}

