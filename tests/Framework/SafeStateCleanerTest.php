<?php

declare(strict_types=1);

namespace WPROG2\Tests\Framework;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WPROG2\Tests\Support\SafeStateCleaner;

final class SafeStateCleanerTest extends TestCase
{
    #[TestDox('Cleanup removes declared runtime state inside var')]
    public function testSafeCleanup(): void
    {
        $root = sys_get_temp_dir() . '/wprog2-cleaner-' . bin2hex(random_bytes(6));
        $var = $root . '/var';
        mkdir($var, 0777, true);
        $state = $var . '/state.txt';
        file_put_contents($state, 'temporary');
        SafeStateCleaner::reset($root, [$state]);
        self::assertFileDoesNotExist($state);
        rmdir($var);
        rmdir($root);
    }

    #[TestDox('Cleanup refuses paths outside the exercise var directory')]
    public function testUnsafeCleanupIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        SafeStateCleaner::assertSafeStatePath('C:/workspace/exercise', 'C:/workspace/other.txt');
    }
}
