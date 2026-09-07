<?php
header('Content-type: text/plain; charset=utf-8');

print_env($_ENV);
print_env($_SERVER);

function print_env($vars){
    $result = "";
    foreach ($vars as $key => $value){
        if(is_scalar($value) && $key != "" && $value != "")
            $result .= "$key=$value";
    }
    echo "$result";
}
