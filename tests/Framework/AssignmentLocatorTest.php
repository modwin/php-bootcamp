<?php

declare(strict_types=1);

namespace WPROG2\Tests\Framework;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use WPROG2\Tests\Support\AssignmentLocator;

final class AssignmentLocatorTest extends TestCase
{
    #[TestDox('Discovery loads a matching exercise configuration and routes')]
    public function testDiscoveryAndConfiguration(): void
    {
        $locator = new AssignmentLocator(WPROG2_ROOT);
        $directory = $locator->find('1.1');
        self::assertNotNull($directory);
        self::assertStringEndsWith('Ex1_1_Secure_File_Handling', str_replace('\\', '/', $directory));
        $configuration = $locator->load('1.1');
        self::assertNotNull($configuration);
        self::assertSame('/counter.php', $configuration->route('main'));
        self::assertNull($locator->find('1.2'));
    }

    #[TestDox('A started exercise without test configuration gives actionable failure')]
    public function testMissingConfigurationFails(): void
    {
        $root = sys_get_temp_dir() . '/wprog2-locator-' . bin2hex(random_bytes(6));
        $directory = $root . '/Assignments/S1_Test/Ex1_2_Missing';
        mkdir($directory, 0777, true);
        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('has no test.config.php');
            (new AssignmentLocator($root))->load('1.2');
        } finally {
            rmdir($directory);
            rmdir(dirname($directory));
            rmdir(dirname($directory, 2));
            rmdir($root);
        }
    }
}
