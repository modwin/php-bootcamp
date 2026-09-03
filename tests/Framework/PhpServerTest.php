<?php

declare(strict_types=1);

namespace WPROG2\Tests\Framework;

use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WPROG2\Tests\Support\PhpServer;

final class PhpServerTest extends TestCase
{
    #[TestDox('The harness starts and stops an isolated PHP web server')]
    public function testServerLifecycle(): void
    {
        $server = new PhpServer();
        try {
            $baseUrl = $server->start(WPROG2_ROOT . '/tests/Fixtures/app/public');
            $response = (new Client())->get($baseUrl, ['http_errors' => false]);
            self::assertSame(200, $response->getStatusCode());
            self::assertSame('fixture-ok', (string) $response->getBody());
        } finally {
            $server->stop();
        }
    }
}
