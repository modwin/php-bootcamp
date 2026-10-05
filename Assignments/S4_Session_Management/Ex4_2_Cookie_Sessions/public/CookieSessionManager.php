<?php

namespace WPROG2\S4_Session_Management\Ex4_2_Cookie_Sessions\public;

use Random\RandomException;

const RANDOM_BYTES = 16;
const MINUTE_IN_SECONDS = 60;
const HOUR_IN_SECONDS = MINUTE_IN_SECONDS * MINUTE_IN_SECONDS;
const HTTP_CODE_INTERNAL_SERVER_ERROR = 500;

class CookieSessionManager
{
    private readonly int $lifespanSeconds;
    private string $cookieName;
    private bool $isSecure;

    public function __construct(int $hours = 3, string $cookieName = 'session_id', bool $isSecure = false)
    {
        $this->cookieName = $cookieName;
        $this->lifespanSeconds = $hours * HOUR_IN_SECONDS;
        $this->isSecure = $isSecure; // 1. Tilldela värdet till egenskapen
    }

    public function getOrCreateSessionId(): ?string
    {
        if (isset($_COOKIE[$this->cookieName])) {
            return $_COOKIE[$this->cookieName];
        }

        try {
            $sessionId = bin2hex(random_bytes(RANDOM_BYTES));
        } catch (RandomException $e) {
            http_response_code(HTTP_CODE_INTERNAL_SERVER_ERROR);
            exit("Error generating session ID.");
        }

        $options = $this->isSecure ? [
            'expires'  => time() + $this->lifespanSeconds,
            'path'     => '/',
            'domain'   => '',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ] : [
            'expires'  => time() + $this->lifespanSeconds,
            'path'     => '/',
            'domain'   => '',
            'secure'   => false,
        ];

        setcookie($this->cookieName, $sessionId, $options);

        return $sessionId;
    }

    public function getLifespanSeconds(): int
    {
        return $this->lifespanSeconds;
    }
}