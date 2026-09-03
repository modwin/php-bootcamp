# WPROG2 test framework

The test suite turns each assignment definition into an executable learning contract. It checks observable behavior without providing the production solution.

## Daily workflow

1. Run `composer test:framework` after installing dependencies.
2. Start an exercise using the repository assignment template and add `test.config.php`.
3. Run `composer test:assignment -- X.Y` and read the TestDox requirements that fail.
4. Implement one failing behavior at a time.
5. Keep rerunning the focused assignment until it is green.
6. Complete the manual rubric and record verification in the exercise README.

Common commands:

```powershell
composer install
composer test:framework
composer test:assignment -- 1.1
composer test
composer test:services
composer test:provisional
```

`composer test` excludes service-backed, externally hosted, and provisional tests. Missing exercise directories are skipped; an existing exercise without `test.config.php` is a configuration error.

## Exercise configuration

Every active exercise supplies `test.config.php`. Paths are relative to the exercise directory.

| Key | Purpose |
|---|---|
| `document_root` | Directory served by an isolated PHP development server. |
| `base_url` | External URL used instead of the PHP server, primarily for Apache/HTTPS assignments. |
| `routes` | Stable aliases used by tests, such as `main`, `form`, `echo`, `image`, `edit`, `feed`, `sources`, or `attachment`. |
| `state_paths` | Files/directories the tests may reset; every path must be below this exercise's `var/`. |
| `environment` | Test-only environment passed to the PHP server process. |
| `services` | Required fixture services: `mail` or `database`. |
| `features` | Optional assignment extensions that should be tested only when enabled. |
| `settings` | Non-secret field names, table names, report paths, limits, and other test bindings. |

The test harness chooses an unused loopback port, starts PHP, waits until it is reachable, and stops it after the class. State is reset before and after each test. Cleanup rejects any path outside the exercise's `var/` directory.

## Docker services

`compose.test.yaml` provides GreenMail SMTP/IMAP, an ephemeral MariaDB database, and a PHP 8.5 runner with the relevant extensions. Start Docker Desktop before a service-backed run. The runner uses fixed test-only credentials, binds published ports to loopback, and removes its containers and volumes afterward.

Use environment variables in production code rather than committing service credentials. A mail assignment can map the framework values in `test.config.php`, for example:

```php
'services' => ['mail'],
'environment' => [
    'SMTP_HOST' => getenv('WPROG2_SMTP_HOST') ?: '127.0.0.1',
    'SMTP_PORT' => getenv('WPROG2_SMTP_PORT') ?: '3025',
],
```

Database assignments similarly receive `WPROG2_DB_DSN`, `WPROG2_DB_USER`, and `WPROG2_DB_PASSWORD`. Use `settings` to declare implementation names such as `entry_table`, `image_table`, or `stored_procedure`.

## Authentication and HTTPS

Assignments 8.1–8.5 test a student-configured web server because the server configuration is itself the assignment. Copy `tests/config.local.php.example` to the ignored `tests/config.local.php`, enter test-only URLs and certificate paths, and expose those values from the exercise's `test.config.php`:

```php
<?php

use WPROG2\Tests\Support\LocalConfiguration;

$local = LocalConfiguration::load()['8.3'] ?? [];

return [
    'base_url' => $local['base_url'] ?? null,
    'routes' => ['main' => '/'],
    'state_paths' => [],
    'environment' => [],
    'services' => [],
    'features' => [],
    'settings' => [
        'ca_cert' => $local['ca_cert'] ?? '',
        'report_file' => 'docs/commands.txt',
    ],
];
```

Never use real passwords or private production certificates.

## Provisional and manual checks

The supplied testcase text contains 24 of the 31 assignments. Tests for 1.3, 1.4, 5.3, 5.4, 7.2, 8.1, and 9 are marked `provisional` and excluded from normal runs until checked against the full official pages.

Automation deliberately does not claim to judge visual quality, report depth, or whether an architectural explanation demonstrates real understanding. Complete [`specs/manual-acceptance.md`](specs/manual-acceptance.md) alongside the executable tests. The complete automated coverage summary is in [`specs/assignment-matrix.md`](specs/assignment-matrix.md).

## Reading failures

- **Failure:** an implemented requirement differs from the contract.
- **Error:** the exercise configuration, process, or fixture service could not run.
- **Skipped:** the exercise has not been started or an optional feature is disabled.
- **Provisional:** the contract must be checked against missing official text before it becomes authoritative.

The current 1.1 program is expected to be red for its MIME type, HTML response body, and missing `flock()` call. This is the first intended learning loop, not a framework installation failure.
