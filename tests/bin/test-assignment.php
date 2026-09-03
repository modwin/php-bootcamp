<?php

declare(strict_types=1);

use Symfony\Component\Process\Process;
use WPROG2\Tests\Support\AssignmentLocator;
use WPROG2\Tests\Support\AssignmentRegistry;
use WPROG2\Tests\Support\ServiceRunner;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

define('WPROG2_ROOT', dirname(__DIR__, 2));

$arguments = array_values(array_slice($argv, 1));
$includeProvisional = in_array('--include-provisional', $arguments, true);
$arguments = array_values(array_filter($arguments, static fn (string $argument): bool => $argument !== '--include-provisional'));
$id = $arguments[0] ?? '';

if ($id === '') {
    fwrite(STDERR, "Usage: composer test:assignment -- <id> [--include-provisional]\n");
    exit(2);
}

try {
    $entry = AssignmentRegistry::get($id);
    if ((new AssignmentLocator(WPROG2_ROOT))->find($id) === null) {
        echo "Assignment {$id} has not been started; its tests are skipped.\n";
        exit(0);
    }
    if ($entry['provisional'] && !$includeProvisional) {
        fwrite(STDERR, "Assignment {$id} has a provisional contract. Re-run with --include-provisional.\n");
        exit(2);
    }

    $phpunitArguments = ['--group', "assignment-{$id}"];
    if ($entry['tier'] === 'service') {
        exit(ServiceRunner::run($entry['services'], $phpunitArguments));
    }

    $process = new Process(
        [PHP_BINARY, WPROG2_ROOT . '/vendor/phpunit/phpunit/phpunit', '--testdox', ...$phpunitArguments],
        WPROG2_ROOT,
    );
    $process->setTimeout(null);
    $process->run(static function (string $type, string $buffer): void {
        echo $buffer;
    });
    exit($process->getExitCode() ?? 1);
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage() . "\n");
    exit(2);
}
