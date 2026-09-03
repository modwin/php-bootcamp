# Assignment 1.1 — Säker filhantering

## Metadata

| Field | Value |
|---|---|
| Category | 1 — Understödjande tekniker |
| Type | Mandatory |
| Status | In progress |
| Related assignment | 3.1 later extends this counter with separated HTML |

## Learning goal

Implement a persistent page-visit counter while learning how concurrent web requests must coordinate access to shared server-side files.

## Requirements checklist

- [ ] Store the total visit count in a server-side file.
- [ ] Return the counter using the `text/plain` MIME type and UTF-8.
- [ ] Return plain text only; do not generate HTML for this assignment.
- [ ] Use `flock()` to protect the complete read–modify–write operation.
- [ ] Ensure concurrent requests cannot lose increments or observe a partially written value.
- [ ] Handle initial file creation and file-operation failures deliberately.

The current program is a work in progress and is not ready for submission: it still returns HTML and does not yet lock the counter file.

## Structure and design

```text
Ex1_1_Secure_File_Handling/
├── README.md
├── public/
│   └── counter.php
└── var/
    └── counter.txt       # generated and ignored
```

Only `public/` is exposed through the development server. Mutable state is kept in `var/`, outside the document root.

## Run locally

From the repository root:

```powershell
php -S 127.0.0.1:8000 -t Assignments/S1_Supporting_Techniques/Ex1_1_Secure_File_Handling/public
```

Open `http://127.0.0.1:8000/counter.php` and stop the server with `Ctrl+C`.

## Verification

| Date | Environment | Check | Result |
|---|---|---|---|
| 2026-09-03 | PHP 8.5.1 | PHP syntax | Passed |
| 2026-09-03 | PHP 8.5.1 built-in server | `Content-Type` is `text/plain` | Failed: currently `text/html`; implementation pending |
| 2026-09-03 | PHP 8.5.1 built-in server | Response contains no HTML | Failed: currently emits an `<h1>`; implementation pending |
| — | — | Counter survives server restart | Not run |
| — | — | Parallel requests increment exactly once each | Not run |
| 2026-09-03 | PHP 8.5.1 built-in server and Git | Counter data remains outside `public/` and Git | Passed |

## Lessons learned

Complete this section while implementing and verifying the assignment.

## Submission artifacts

- `public/counter.php`
- This README as the local implementation and verification record
- Do not submit the generated `var/counter.txt`
