<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use PDO;
use RuntimeException;

final class DatabaseProbe
{
    public static function connection(): PDO
    {
        $dsn = getenv('WPROG2_DB_DSN');
        if ($dsn === false || $dsn === '') {
            throw new RuntimeException('WPROG2_DB_DSN is not configured. Run the service test command.');
        }
        return new PDO(
            $dsn,
            getenv('WPROG2_DB_USER') ?: 'wprog2',
            getenv('WPROG2_DB_PASSWORD') ?: 'wprog2-test-only',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC],
        );
    }

    public static function quotedIdentifier(string $identifier): string
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier) !== 1) {
            throw new RuntimeException("Unsafe SQL identifier in test configuration: {$identifier}");
        }
        return "`{$identifier}`";
    }
}
