<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use RuntimeException;
use Symfony\Component\Process\Process;

final class ServiceRunner
{
    /** @param list<string> $phpunitArguments */
    public static function run(array $services, array $phpunitArguments): int
    {
        self::assertDockerAvailable();
        $compose = ['docker', 'compose', '--project-name', 'wprog2-tests', '--file', 'compose.test.yaml'];
        $services = array_values(array_unique($services));
        try {
            if ($services !== []) {
                self::mustRun([...$compose, 'up', '--detach', '--wait', ...$services]);
            }
            $command = [...$compose, 'run', '--rm', 'test-runner', 'php', 'vendor/phpunit/phpunit/phpunit', '--testdox', ...$phpunitArguments];
            $process = new Process($command, WPROG2_ROOT);
            $process->setTimeout(null);
            $process->run(static function (string $type, string $buffer): void {
                echo $buffer;
            });
            return $process->getExitCode() ?? 1;
        } finally {
            $cleanup = new Process([...$compose, 'down', '--volumes', '--remove-orphans'], WPROG2_ROOT);
            $cleanup->setTimeout(120);
            $cleanup->run();
        }
    }

    private static function assertDockerAvailable(): void
    {
        $process = new Process(['docker', 'info'], WPROG2_ROOT);
        $process->setTimeout(15);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException('Docker is unavailable. Start Docker Desktop and try again.');
        }
    }

    /** @param list<string> $command */
    private static function mustRun(array $command): void
    {
        $process = new Process($command, WPROG2_ROOT);
        $process->setTimeout(300);
        $process->run(static function (string $type, string $buffer): void {
            echo $buffer;
        });
        if (!$process->isSuccessful()) {
            throw new RuntimeException('Service command failed: ' . $process->getErrorOutput());
        }
    }
}
