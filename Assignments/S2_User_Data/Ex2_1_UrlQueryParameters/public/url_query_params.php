<?php

namespace WPROG2\Tests\Assignments\S2_User_Data\Ex2_1_UrlQueryParameters;

header('Content-type: text/plain; charset=utf-8');

function print_get_params(): void
{

    foreach ($_GET as $keys => $value){
        echo "$keys: $value\n";
    }
}

print_get_params();

