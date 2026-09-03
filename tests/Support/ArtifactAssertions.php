<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use PHPUnit\Framework\Assert;

final class ArtifactAssertions
{
    public static function requiredFile(AssignmentConfiguration $config, string $setting): string
    {
        $relative = $config->settings[$setting] ?? null;
        Assert::assertIsString($relative, "test.config.php must define settings['{$setting}'].");
        $path = $config->exerciseDirectory . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
        Assert::assertFileExists($path);
        return $path;
    }

    public static function assertNonEmptyUtf8Text(string $file): string
    {
        $contents = file_get_contents($file);
        Assert::assertIsString($contents);
        Assert::assertNotSame('', trim($contents), "Expected a non-empty report: {$file}");
        Assert::assertTrue(mb_check_encoding($contents, 'UTF-8'), "Report is not UTF-8: {$file}");
        return $contents;
    }
}
