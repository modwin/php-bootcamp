<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class SafeStateCleaner
{
    public static function assertSafeStatePath(string $exerciseDirectory, string $path): void
    {
        $varDirectory = self::normalize($exerciseDirectory . DIRECTORY_SEPARATOR . 'var');
        $candidate = self::normalize($path);
        if ($candidate === $varDirectory || !str_starts_with($candidate, $varDirectory . DIRECTORY_SEPARATOR)) {
            throw new InvalidArgumentException("Resettable state must be a child of the exercise var directory: {$path}");
        }
        if (basename($candidate) === '.gitignore') {
            throw new InvalidArgumentException('The var/.gitignore file cannot be reset.');
        }
    }

    /** @param list<string> $paths */
    public static function reset(string $exerciseDirectory, array $paths): void
    {
        foreach ($paths as $path) {
            self::assertSafeStatePath($exerciseDirectory, $path);
            self::remove($path);
        }
    }

    private static function remove(string $path): void
    {
        if (is_link($path) || is_file($path)) {
            if (!unlink($path)) {
                throw new InvalidArgumentException("Unable to remove test state: {$path}");
            }
            return;
        }
        if (!is_dir($path)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );
        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            $item->isDir() && !$item->isLink() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($path);
    }

    private static function normalize(string $path): string
    {
        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts = [];
        foreach (explode(DIRECTORY_SEPARATOR, $normalized) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }
        $prefix = preg_match('/^[A-Za-z]:/', $normalized) === 1
            ? strtolower(array_shift($parts)) . DIRECTORY_SEPARATOR
            : DIRECTORY_SEPARATOR;
        return $prefix . implode(DIRECTORY_SEPARATOR, array_map('strtolower', $parts));
    }
}
