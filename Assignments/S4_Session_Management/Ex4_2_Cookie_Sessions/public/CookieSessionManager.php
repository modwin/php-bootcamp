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

    function __construct(int $hours = 3, string $cookieName = 'session_id'){
        $this->cookieName = $cookieName;
        $this->lifespanSeconds = $hours * HOUR_IN_SECONDS; // Hour in seconds * total amount of hours.
    }

    public function getOrCreateSessionId(): ?string
    {
        if(isset($_COOKIE["$this->cookieName"])){
            return $_COOKIE["$this->cookieName"];
        }
        try {
            $session_id = bin2hex(random_bytes(RANDOM_BYTES));
        } catch (RandomException $e) {
            http_response_code(HTTP_CODE_INTERNAL_SERVER_ERROR);
            exit("Error generating session ID.");
        }

        setcookie($this->cookieName,
            $session_id, time() +
            $this->lifespanSeconds, "/");

        return $session_id;
    }

    public function getLifespanSeconds(): int
    {
        return $this->lifespanSeconds;
    }

}