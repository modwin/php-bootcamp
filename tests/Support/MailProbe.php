<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use RuntimeException;

final class MailProbe
{
    public function __construct(
        private readonly string $host,
        private readonly int $smtpPort,
        private readonly int $imapPort,
        private readonly string $user,
        private readonly string $password,
    ) {
    }

    public static function fromEnvironment(): self
    {
        return new self(
            getenv('WPROG2_SMTP_HOST') ?: '127.0.0.1',
            (int) (getenv('WPROG2_SMTP_PORT') ?: 3025),
            (int) (getenv('WPROG2_IMAP_PORT') ?: 3143),
            getenv('WPROG2_MAIL_USER') ?: 'student@test.local',
            getenv('WPROG2_MAIL_PASSWORD') ?: 'student',
        );
    }

    public function purge(): void
    {
        $socket = $this->imapLogin();
        try {
            $this->imapCommand($socket, 'A003', 'SELECT INBOX');
            $this->imapCommand($socket, 'A004', 'STORE 1:* +FLAGS (\\Deleted)');
            $this->imapCommand($socket, 'A005', 'EXPUNGE');
            $this->imapCommand($socket, 'A006', 'LOGOUT');
        } finally {
            fclose($socket);
        }
    }

    /** @return list<string> */
    public function messages(): array
    {
        $socket = $this->imapLogin();
        try {
            $this->imapCommand($socket, 'A003', 'SELECT INBOX');
            $search = $this->imapCommand($socket, 'A004', 'SEARCH ALL');
            preg_match('/^\\* SEARCH(?: (.*))?$/mi', $search, $match);
            $ids = isset($match[1]) ? preg_split('/\\s+/', trim($match[1])) : [];
            $messages = [];
            foreach (array_filter($ids ?: []) as $index => $id) {
                $messages[] = $this->imapCommand($socket, 'F' . ($index + 10), "FETCH {$id} BODY.PEEK[]");
            }
            $this->imapCommand($socket, 'A999', 'LOGOUT');
            return $messages;
        } finally {
            fclose($socket);
        }
    }

    public function sendFixture(string $subject, string $body, ?string $rawAttachment = null): void
    {
        $boundary = 'wprog2-' . bin2hex(random_bytes(8));
        $headers = [
            'From: fixture@test.local',
            'To: ' . $this->user,
            'Subject: ' . $subject,
            'MIME-Version: 1.0',
        ];
        if ($rawAttachment === null) {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
            $message = implode("\r\n", $headers) . "\r\n\r\n" . $body;
        } else {
            $headers[] = "Content-Type: multipart/mixed; boundary=\"{$boundary}\"";
            $message = implode("\r\n", $headers) . "\r\n\r\n"
                . "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n{$body}\r\n"
                . "--{$boundary}\r\nContent-Type: application/octet-stream\r\n"
                . "Content-Disposition: attachment; filename=\"fixture.bin\"\r\n"
                . "Content-Transfer-Encoding: base64\r\n\r\n"
                . chunk_split(base64_encode($rawAttachment))
                . "--{$boundary}--\r\n";
        }

        $socket = $this->connect($this->smtpPort);
        try {
            $this->expectSmtp($socket, 220);
            $this->smtpCommand($socket, 'EHLO wprog2.test', 250);
            $this->smtpCommand($socket, 'MAIL FROM:<fixture@test.local>', 250);
            $this->smtpCommand($socket, 'RCPT TO:<' . $this->user . '>', 250);
            $this->smtpCommand($socket, 'DATA', 354);
            fwrite($socket, str_replace("\r\n.\r\n", "\r\n..\r\n", $message) . "\r\n.\r\n");
            $this->expectSmtp($socket, 250);
            $this->smtpCommand($socket, 'QUIT', 221);
        } finally {
            fclose($socket);
        }
    }

    /** @return resource */
    private function imapLogin()
    {
        $socket = $this->connect($this->imapPort);
        $greeting = fgets($socket);
        if ($greeting === false || !str_starts_with($greeting, '* OK')) {
            throw new RuntimeException('Unexpected IMAP greeting: ' . (string) $greeting);
        }
        $this->imapCommand($socket, 'A001', 'LOGIN ' . $this->quote($this->user) . ' ' . $this->quote($this->password));
        return $socket;
    }

    /** @param resource $socket */
    private function imapCommand($socket, string $tag, string $command): string
    {
        fwrite($socket, "{$tag} {$command}\r\n");
        $response = '';
        while (($line = fgets($socket)) !== false) {
            $response .= $line;
            if (preg_match('/^' . preg_quote($tag, '/') . ' (OK|NO|BAD)/i', $line, $match) === 1) {
                if (strtoupper($match[1]) !== 'OK') {
                    throw new RuntimeException("IMAP command failed: {$response}");
                }
                return $response;
            }
        }
        throw new RuntimeException("IMAP connection closed during {$command}.");
    }

    /** @return resource */
    private function connect(int $port)
    {
        $socket = @fsockopen($this->host, $port, $errorCode, $errorMessage, 5);
        if ($socket === false) {
            throw new RuntimeException("Unable to connect to mail service: {$errorMessage} ({$errorCode})");
        }
        stream_set_timeout($socket, 5);
        return $socket;
    }

    /** @param resource $socket */
    private function smtpCommand($socket, string $command, int $expected): void
    {
        fwrite($socket, $command . "\r\n");
        $this->expectSmtp($socket, $expected);
    }

    /** @param resource $socket */
    private function expectSmtp($socket, int $expected): void
    {
        $response = '';
        do {
            $line = fgets($socket);
            if ($line === false) {
                throw new RuntimeException('SMTP connection closed unexpectedly.');
            }
            $response .= $line;
        } while (isset($line[3]) && $line[3] === '-');
        if ((int) substr($response, 0, 3) !== $expected) {
            throw new RuntimeException("Unexpected SMTP response: {$response}");
        }
    }

    private function quote(string $value): string
    {
        return '"' . addcslashes($value, "\\\"") . '"';
    }
}
