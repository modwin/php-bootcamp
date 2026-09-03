<?php

declare(strict_types=1);

use WPROG2\Tests\Support\ServiceRunner;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

define('WPROG2_ROOT', dirname(__DIR__, 2));

try {
    exit(ServiceRunner::run(['mail', 'database'], ['--group', 'service']));
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage() . "\n");
    exit(2);
}
