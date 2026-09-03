# Manual acceptance rubrics

Automated tests are evidence, not a substitute for reviewing the course requirements or understanding the implementation. Copy relevant results into the assignment README.

## All HTML assignments

- [ ] Validate final HTML with the W3C Markup Validation Service.
- [ ] Validate CSS with the W3C CSS Validation Service where CSS is present.
- [ ] Verify keyboard use, readable error messages, and sensible behavior without JavaScript.
- [ ] Confirm that generated pages remain understandable at narrow and wide viewport sizes.

## External systems

- [ ] Mail tests use only sandbox addresses and cannot send to the public internet.
- [ ] Database credentials are test-only, parameterized, and excluded from Git.
- [ ] Authentication reports contain every command and path needed to reproduce the setup.
- [ ] HTTPS tests show the certificate chain, identity, expiry, and both successful and rejected connections.
- [ ] Private keys, credential files, runtime databases, and generated certificates are not tracked.

## Assignment 6.4

- [ ] The report explains why stored procedures can reduce repeated parsing/round trips.
- [ ] The report distinguishes a persistent PHP connection from a real managed connection pool.
- [ ] Commands and configuration are detailed enough to reproduce the database and pooling setup.
- [ ] Evidence shows that the web application actually uses both selected techniques.

## Assignment 8.6

- [ ] The plain-text report is at least two A4 pages when rendered with the chosen normal text settings.
- [ ] It compares several realistic payment approaches, rather than listing names only.
- [ ] It discusses trust boundaries, transport security, authentication, fraud, refunds, and handling of sensitive payment data.
- [ ] Claims are current, sourced, and clearly separated from personal recommendations.

## Assignment 9 — Gesällprov

- [ ] The application is independently conceived and not merely an earlier exercise.
- [ ] Form and function are both polished and appropriate for the intended audience.
- [ ] The PDF title occupies approximately two lines and names both project and creator.
- [ ] The summary is 5–10 lines and contains text and an informative image.
- [ ] `Framtagning`, `Konstruktion`, and `Funktion` each contain at least two substantive pages with the required text/images.
- [ ] Development evidence shows meaningful iterations, not a reconstructed story written afterward.
- [ ] The construction section explains the system and includes course-compliant pseudocode.
- [ ] The functionality section demonstrates normal use, invalid input, and important failure cases.
- [ ] Every earlier technique claimed in `test.config.php` is actually present and retested.
