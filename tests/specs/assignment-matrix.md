# Automated assignment contract matrix

Modes: **local** uses an isolated PHP server, **service** uses Docker fixtures, **external** targets the configured web server, and **provisional** requires confirmation against missing official text.

| ID | Mode | Core executable contract |
|---|---|---|
| 1.1 | local | Plain numeric response, persistence, `flock()`, and multi-process concurrency. |
| 1.2 | local | All environment name/value pairs as UTF-8 plain text. |
| 1.3 | local, provisional | Declared and decodable generated image. |
| 1.4 | local, provisional | HTTP client-pull `Refresh` behavior. |
| 2.1 | local | Query-bearing link and arbitrary GET pair round-trip. |
| 2.2 | local | Diverse form plus equivalent arbitrary GET/POST handling. |
| 2.3 | local | Allowed file bytes/MIME, unsupported metadata, basename safety, and size limit. |
| 3.1 | local | Locked HTML counter, fixed marker replacement, and source separation. |
| 3.2 | local | Escaped variable rows, repeat markers, and source separation. |
| 4.1 | local | One embedded ID across links/forms without built-in sessions. |
| 4.2 | local | Three-hour client cookie and continuity without built-in sessions. |
| 4.3 | local | Built-in session continuity and client isolation. |
| 5.1 | service | Required form fields and captured SMTP headers/body/warning. |
| 5.2 | service | At least two exact captured attachments. |
| 5.3 | service, provisional | Seeded plain IMAP message listing and reading. |
| 5.4 | service, provisional | Received attachment metadata and exact download. |
| 6.1 | local | Persistent time/address/agent visit log. |
| 6.2 | service | Guestbook persistence, safe links, HTML neutralization, and SQL injection. |
| 6.3 | service | Exact image storage/response and forced second-insert rollback. |
| 6.4 | service | Stored procedure use, persistent connection configuration, and report artifact. |
| 7.1 | local | Wiki publish/edit cycle and script-injection resistance. |
| 7.2 | local, provisional | Well-formed syndication feed metadata and entries. |
| 7.3 | local | Add/remove deterministic feed sources and handle malformed sources. |
| 7.4 | local | Cycle-safe local crawling, relative links, matching, and depth limit. |
| 8.1 | external, provisional | Basic challenge, rejection, authentication, username, and report. |
| 8.2 | external | Digest challenge, rejection, authentication, username, and report. |
| 8.3 | external | Trusted server certificate succeeds; untrusted certificate fails. |
| 8.4 | external | Anonymous client fails; trusted client certificate succeeds. |
| 8.5 | external | Secure three-hour cookie works through HTTPS and is withheld from HTTP. |
| 8.6 | local | UTF-8 payment comparison and security report structure. |
| 9 | local, provisional | Main application smoke test, declared reused techniques, and PDF structure. |
