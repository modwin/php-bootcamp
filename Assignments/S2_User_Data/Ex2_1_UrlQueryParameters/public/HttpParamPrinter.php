<?php

namespace WPROG2\S2_User_Data\Ex2_1_UrlQueryParameters\public;

header('Content-type: text/plain; charset=utf-8');

class HttpParamPrinter
{
    public static function printParams(array $params): void
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            echo "$key: $value\n";
        }
    }
}

