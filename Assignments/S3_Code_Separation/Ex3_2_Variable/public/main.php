<?php

namespace WPROG2\S3_Code_Separation\Ex3_2_Variable;


use WPROG2\S3_Code_Separation\Ex3_2_Variable\public\BlockTemplateEngine;

require_once __DIR__ . '/../../../../vendor/autoload.php';

$engine = new BlockTemplateEngine(__DIR__ . '/env_table.html');
$result = $engine->render($_SERVER);

echo $result;





