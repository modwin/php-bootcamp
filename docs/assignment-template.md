# Assignment X.Y — Official Swedish Title

## Metadata

| Field | Value |
|---|---|
| Category | X — Category name |
| Type | Mandatory / Optional |
| Status | Not started |
| Related assignment | None |

## Learning goal

Explain in your own words what this assignment is intended to teach and what you expect to understand after completing it.

## Requirements checklist

- [ ] Rewrite each official requirement as a separate, testable statement.
- [ ] Keep HTML and server-side code in separate files when HTML is required.
- [ ] Add assignment-specific security and failure-handling requirements.
- [ ] Identify the required response MIME type.

## Structure and design

Describe the chosen files, data flow, and why the structure exposes the course technique clearly. Record any deliberate extension beyond the minimum assignment.

Add a `test.config.php` when the exercise directory is created. The file binds implementation-specific filenames to stable names used by the course tests:

```php
<?php

declare(strict_types=1);

return [
    'document_root' => 'public',
    'routes' => [
        'main' => '/index.php',
    ],
    'state_paths' => [
        'var/generated-state.json',
    ],
    'environment' => [],
    'services' => [],
    'features' => [],
    'settings' => [],
];
```

Only list resettable files beneath this exercise's `var/` directory. Optional course extensions belong in `features` and remain untested until explicitly enabled. See [`tests/README.md`](../tests/README.md) for route aliases and external-service examples.

## Run locally

Document prerequisites, configuration examples, the exact server command, and the URL to open. Never record real passwords or secret values.

```powershell
php -S 127.0.0.1:8000 -t path/to/exercise/public
composer test:assignment -- X.Y
```

## Verification

| Date | Environment | Check | Result |
|---|---|---|---|
| YYYY-MM-DD | PHP x.y | Example check | Not run |

Include syntax checks, HTTP headers, normal behavior, invalid input, relevant concurrency or security cases, and W3C validation where applicable.

## Lessons learned

Record what was difficult, what changed during implementation, and what you would improve after reviewing the result.

## Submission artifacts

List every source, configuration example, report, screenshot, or other file required for submission. Confirm that generated runtime data and secrets are excluded.
