<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use InvalidArgumentException;

final readonly class AssignmentConfiguration
{
    /**
     * @param array<string, string> $routes
     * @param list<string> $statePaths
     * @param array<string, string> $environment
     * @param list<string> $services
     * @param array<string, bool> $features
     * @param array<string, mixed> $settings
     */
    private function __construct(
        public string $id,
        public string $exerciseDirectory,
        public ?string $documentRoot,
        public ?string $baseUrl,
        public array $routes,
        public array $statePaths,
        public array $environment,
        public array $services,
        public array $features,
        public array $settings,
    ) {
    }

    public static function fromFile(string $id, string $exerciseDirectory, string $file): self
    {
        $values = require $file;
        if (!is_array($values)) {
            throw new InvalidArgumentException("{$file} must return an array.");
        }

        $documentRoot = self::optionalPath($values['document_root'] ?? null, $exerciseDirectory);
        $baseUrl = isset($values['base_url']) && is_string($values['base_url']) && $values['base_url'] !== ''
            ? rtrim($values['base_url'], '/') . '/'
            : null;
        if ($documentRoot === null && $baseUrl === null) {
            throw new InvalidArgumentException("Assignment {$id} needs document_root or base_url.");
        }
        if ($documentRoot !== null && !is_dir($documentRoot)) {
            throw new InvalidArgumentException("Document root does not exist: {$documentRoot}");
        }

        $routes = self::stringMap($values['routes'] ?? [], 'routes');
        if ($routes === []) {
            throw new InvalidArgumentException("Assignment {$id} must declare at least one named route.");
        }

        $statePaths = [];
        foreach ($values['state_paths'] ?? [] as $path) {
            if (!is_string($path)) {
                throw new InvalidArgumentException('state_paths must contain strings.');
            }
            $resolved = self::path($path, $exerciseDirectory);
            SafeStateCleaner::assertSafeStatePath($exerciseDirectory, $resolved);
            $statePaths[] = $resolved;
        }

        return new self(
            $id,
            $exerciseDirectory,
            $documentRoot,
            $baseUrl,
            $routes,
            $statePaths,
            self::stringMap($values['environment'] ?? [], 'environment'),
            self::stringList($values['services'] ?? [], 'services'),
            self::boolMap($values['features'] ?? [], 'features'),
            is_array($values['settings'] ?? []) ? $values['settings'] : [],
        );
    }

    public function route(string $alias): string
    {
        return $this->routes[$alias]
            ?? throw new InvalidArgumentException("Assignment {$this->id} does not define route '{$alias}'.");
    }

    public function featureEnabled(string $feature): bool
    {
        return $this->features[$feature] ?? false;
    }

    private static function optionalPath(mixed $path, string $base): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        if (!is_string($path)) {
            throw new InvalidArgumentException('document_root must be a string.');
        }
        return self::path($path, $base);
    }

    private static function path(string $path, string $base): string
    {
        if (preg_match('~^(?:[A-Za-z]:[\\\\/]|/)~', $path) === 1) {
            return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        }
        return $base . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    /** @return array<string, string> */
    private static function stringMap(mixed $value, string $name): array
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException("{$name} must be an array.");
        }
        foreach ($value as $key => $item) {
            if (!is_string($key) || !is_string($item)) {
                throw new InvalidArgumentException("{$name} must map strings to strings.");
            }
        }
        return $value;
    }

    /** @return list<string> */
    private static function stringList(mixed $value, string $name): array
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException("{$name} must be an array.");
        }
        foreach ($value as $item) {
            if (!is_string($item)) {
                throw new InvalidArgumentException("{$name} must contain strings.");
            }
        }
        return array_values($value);
    }

    /** @return array<string, bool> */
    private static function boolMap(mixed $value, string $name): array
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException("{$name} must be an array.");
        }
        foreach ($value as $key => $item) {
            if (!is_string($key) || !is_bool($item)) {
                throw new InvalidArgumentException("{$name} must map strings to booleans.");
            }
        }
        return $value;
    }
}
