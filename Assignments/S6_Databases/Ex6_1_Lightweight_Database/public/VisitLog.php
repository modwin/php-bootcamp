<?php

namespace WPROG2\S6_Databases\Ex6_1_Lightweight_Database\public;

class VisitLog
{
    public function __construct(
        private readonly string $currentTime,
        private readonly string $remoteAddress,
        private readonly string $httpUserAgent
    ) {}

    public function getCurrentTime(): string
    {
        return $this->currentTime;
    }

    public function getRemoteAddress(): string
    {
        return $this->remoteAddress;
    }

    public function getHttpUserAgent(): string
    {
        return $this->httpUserAgent;
    }

    public function __toString(): string
    {
        return
            "TID: {$this->currentTime}\n" .
            "REMOTE_ADDR: {$this->remoteAddress}\n" .
            "HTTP_USER_AGENT: {$this->httpUserAgent}\n";
    }

}