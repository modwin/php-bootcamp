# PHP Bootcamp

This repository contains the practical assignments in PHP concepts. The assignments focus on server-side web development, primarily with PHP and SQL, while keeping HTML and server-side code in separate files.

## Learning workflow

For each assignment:

1. Read the complete official assignment before looking for a solution.
2. Create an exercise directory from [`docs/assignment-template.md`](docs/assignment-template.md).
3. Rewrite the requirements as a checklist in your own words.
4. Implement one requirement at a time and keep runtime data outside `public/`.
5. Verify syntax, HTTP headers, output, failure cases, and any assignment-specific security behavior.
6. Record the commands used, results, and lessons learned in the exercise README.
7. Update the progress table below only after completing the verification checklist.

Statuses used below are **Not started**, **In progress**, **Ready for review**, and **Complete**. “Complete” means the implementation and its documented verification both satisfy the current course assignment.

## Repository convention

Categories use `S<number>_<English_Name>`. Exercise directories are created only when work starts and use `Ex<category>_<exercise>_<English_Description>`.

An exercise may contain:

```text
Ex1_1_Secure_File_Handling/
├── README.md       # requirements, commands, verification, and reflection
├── public/         # web-accessible PHP endpoints and static assets
├── resources/      # pure HTML templates and other non-public inputs
├── var/            # ignored counters, uploads, databases, and generated state
├── tests/          # optional automated checks
└── docs/           # assignment-specific reports and evidence
```

Create only the directories an exercise actually needs. Each solution must remain independently understandable and runnable. Shared frameworks, routers, or template engines should not hide the technique that the assignment is intended to demonstrate.

Never commit passwords, real environment files, private keys, generated certificates, uploads, or runtime databases. Commit safe examples, database schemas, source files, documentation, and required submission artifacts such as `beskrivning.pdf`.

## Running an assignment

Use the exercise's `public/` directory as the document root. For assignment 1.1:

```powershell
php -S 127.0.0.1:8000 -t Assignments/S1_Supporting_Techniques/Ex1_1_Secure_File_Handling/public
```

Then open `http://127.0.0.1:8000/counter.php`. Stop the development server with `Ctrl+C`.

Useful verification commands include:

```powershell
php -l Assignments/S1_Supporting_Techniques/Ex1_1_Secure_File_Handling/public/counter.php
curl.exe -i http://127.0.0.1:8000/counter.php
composer validate --no-check-publish
```

HTML and CSS assignments should also be checked with the W3C validators. Record meaningful manual tests and failure cases in the exercise README rather than relying only on a successful browser view.

## Test-driven learning

The repository includes executable specifications for every assignment. Tests for an exercise activate when its `Ex...` directory and `test.config.php` exist; assignments that have not been started are shown as skipped.

```powershell
composer test:framework
composer test:assignment -- 1.1
composer test
```

The first command verifies the test harness. The second gives a focused red–green loop for one assignment. The third runs all active local, authoritative assignments. Email and MariaDB tests use `composer test:services` and require Docker Desktop. Authentication and HTTPS tests use URLs and certificates from an ignored `tests/config.local.php`.
|---|---|---|---|---|
| 9 | Gesällprov | Mandatory | Not started | — |
