# WPROG2 — Web Development II

This repository contains the practical assignments for the university course WPROG2. The course focuses on server-side web development, primarily with PHP and SQL, while keeping HTML and server-side code in separate files.

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

Tests for assignments whose full definition was not present in the supplied reference are marked provisional and run only with `composer test:provisional` or `--include-provisional`. See [`tests/README.md`](tests/README.md) for configuration, service, and troubleshooting details.

## Assignment progress

Official Swedish titles are retained so that entries can be matched directly to the course website.

### 1 Understödjande tekniker

See [`S1_Supporting_Techniques`](Assignments/S1_Supporting_Techniques/README.md).

| ID | Official title | Type | Status | Work |
|---|---|---|---|---|
| 1.1 | Säker filhantering | Mandatory | In progress | [Open](Assignments/S1_Supporting_Techniques/Ex1_1/README.md) |
| 1.2 | Omgivningsvariabler | Mandatory | Not started | — |
| 1.3 | Grafikgenerering | Optional | Not started | — |
| 1.4 | Klientstyrd omladdning | Optional | Not started | — |

### 2 Information från användaren

See [`S2_User_Data`](Assignments/S2_User_Data/README.md).

| ID | Official title | Type | Status | Work |
|---|---|---|---|---|
| 2.1 | Information sänd via adressfält och länkar | Mandatory | Not started | — |
| 2.2 | Information sänd via formulär | Mandatory | Not started | — |
| 2.3 | Uppladdning av fil | Mandatory | Not started | — |

### 3 Kodseparation

See [`S3_Code_Separation`](Assignments/S3_Code_Separation/README.md).

| ID | Official title | Type | Status | Work |
|---|---|---|---|---|
| 3.1 | Kodseparation med konstant informationsmängd | Mandatory | Not started | — |
| 3.2 | Kodseparation med variabel informationsmängd | Mandatory | Not started | — |

### 4 Sessionshantering

See [`S4_Session_Management`](Assignments/S4_Session_Management/README.md).

| ID | Official title | Type | Status | Work |
|---|---|---|---|---|
| 4.1 | Information inbakad i HTML | Mandatory | Not started | — |
| 4.2 | Information hos klienten | Mandatory | Not started | — |
| 4.3 | Användning av inbyggt stöd | Mandatory | Not started | — |

### 5 Meddelandehantering

See [`S5_Message_Handling`](Assignments/S5_Message_Handling/README.md).

| ID | Official title | Type | Status | Work |
|---|---|---|---|---|
| 5.1 | Epost-sändning utan bifogade filer | Mandatory | Not started | — |
| 5.2 | Epost-sändning med bifogade filer | Mandatory | Not started | — |
| 5.3 | Epost-mottagning utan bifogade filer | Optional | Not started | — |
| 5.4 | Epost-mottagning med bifogade filer | Optional | Not started | — |

### 6 Databaser

See [`S6_Databases`](Assignments/S6_Databases/README.md).

| ID | Official title | Type | Status | Work |
|---|---|---|---|---|
| 6.1 | Lättviktsdatabaser | Mandatory | Not started | — |
| 6.2 | Relationsdatabaser och säkerhet | Mandatory | Not started | — |
| 6.3 | Relationsdatabaser och transaktioner | Mandatory | Not started | — |
| 6.4 | Relationsdatabaser och effektivitet | Optional | Not started | — |

### 7 Innehållshantering

See [`S7_Content_Management`](Assignments/S7_Content_Management/README.md).

| ID | Official title | Type | Status | Work |
|---|---|---|---|---|
| 7.1 | Publiceringssystem | Optional | Not started | — |
| 7.2 | Syndikering | Optional | Not started | — |
| 7.3 | Kanaler | Optional | Not started | — |
| 7.4 | Sökmotor | Optional | Not started | — |

### 8 Säkerhet

See [`S8_Security`](Assignments/S8_Security/README.md).

| ID | Official title | Type | Status | Work |
|---|---|---|---|---|
| 8.1 | HTTP-baserad autentisering med okrypterad information | Optional | Not started | — |
| 8.2 | HTTP-baserad autentisering med krypterad information | Optional | Not started | — |
| 8.3 | HTTPS-baserad konfidentialitet och serversides-autentisering | Optional | Not started | — |
| 8.4 | HTTPS-baserad konfidentialitet och klientsides-autentisering | Optional | Not started | — |
| 8.5 | HTTPS-baserade säkra kakor | Mandatory | Not started | — |
| 8.6 | HTTPS-baserad betalningshantering | Optional | Not started | — |

### 9 Gesällprov

See [`S9_Capstone`](Assignments/S9_Capstone/README.md).

| ID | Official title | Type | Status | Work |
|---|---|---|---|---|
| 9 | Gesällprov | Mandatory | Not started | — |
