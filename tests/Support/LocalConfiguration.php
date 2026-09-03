<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

final class LocalConfiguration
{
    /** @return array<string, mixed> */
    public static function load(): array
    {
        $file = WPROG2_ROOT . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'config.local.php';
        if (!is_file($file)) {
            return [];
        }
        $values = require $file;
        return is_array($values) ? $values : [];
    }
}
