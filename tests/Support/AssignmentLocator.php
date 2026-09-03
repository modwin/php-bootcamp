<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use RuntimeException;

final readonly class AssignmentLocator
{
    public function __construct(private string $projectRoot)
    {
    }

    public function find(string $id): ?string
    {
        AssignmentRegistry::get($id);
        $section = explode('.', $id)[0];
        $exercisePrefix = str_replace('.', '_', $id);
        $pattern = $this->projectRoot
            . DIRECTORY_SEPARATOR . 'Assignments'
            . DIRECTORY_SEPARATOR . "S{$section}_*"
            . DIRECTORY_SEPARATOR . "Ex{$exercisePrefix}_*";
        $matches = array_values(array_filter(glob($pattern) ?: [], 'is_dir'));
        if (count($matches) > 1) {
            throw new RuntimeException("Multiple exercise directories match assignment {$id}: " . implode(', ', $matches));
        }
        return $matches[0] ?? null;
    }

    public function load(string $id): ?AssignmentConfiguration
    {
        $directory = $this->find($id);
        if ($directory === null) {
            return null;
        }
        $configFile = $directory . DIRECTORY_SEPARATOR . 'test.config.php';
        if (!is_file($configFile)) {
            throw new RuntimeException(
                "Assignment {$id} exists but has no test.config.php. Copy the example from docs/assignment-template.md.",
            );
        }
        return AssignmentConfiguration::fromFile($id, $directory, $configFile);
    }
}
