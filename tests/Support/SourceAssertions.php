<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use PHPUnit\Framework\Assert;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class SourceAssertions
{
    /** @return list<string> */
    public static function filesWithExtension(string $root, string $extension): array
    {
        if (!is_dir($root)) {
            return [];
        }
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            if (!$item->isFile() || strtolower($item->getExtension()) !== strtolower($extension)) {
                continue;
            }
            $path = $item->getPathname();
            if (str_contains($path, DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR)
                || str_contains($path, DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR)
                || basename($path) === 'test.config.php') {
                continue;
            }
            $files[] = $path;
        }
        sort($files);
        return $files;
    }

    public static function assertFunctionCallExists(string $root, string $function): void
    {
        foreach (self::filesWithExtension($root, 'php') as $file) {
            $source = file_get_contents($file);
            if ($source !== false && preg_match('/\\b' . preg_quote($function, '/') . '\\s*\\(/i', $source) === 1) {
                Assert::assertTrue(true);
                return;
            }
        }
        Assert::fail("Expected a call to {$function}() in the assignment PHP source.");
    }

    public static function assertHtmlFilesContainNoPhp(string $root): void
    {
        $files = self::filesWithExtension($root, 'html');
        Assert::assertNotEmpty($files, 'Expected at least one separate HTML document.');
        foreach ($files as $file) {
            Assert::assertStringNotContainsString('<?', (string) file_get_contents($file), "PHP found in {$file}");
        }
    }

    public static function assertPhpFilesContainNoStructuralHtml(string $root): void
    {
        $pattern = '/<(?:!doctype|html|head|body|table|form|h[1-6]|p|div|tr|td|img|a)\\b/i';
        foreach (self::filesWithExtension($root, 'php') as $file) {
            Assert::assertDoesNotMatchRegularExpression(
                $pattern,
                (string) file_get_contents($file),
                "Structural HTML found in PHP source {$file}",
            );
        }
    }
}
