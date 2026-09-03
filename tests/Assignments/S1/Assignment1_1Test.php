<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S1;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\SourceAssertions;

#[Group('assignment-1.1')]
#[Group('local')]
final class Assignment1_1Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '1.1';

    #[TestDox('The counter declares a plain UTF-8 response')]
    public function testPlainTextResponseHeader(): void
    {
        $response = $this->request('GET');
        self::assertSame(200, $response->getStatusCode());
        $this->assertContentType($response, 'text/plain');
    }

    #[TestDox('The counter body is one integer without HTML')]
    public function testPlainIntegerBody(): void
    {
        $response = $this->request('GET');
        $body = trim((string) $response->getBody());
        self::assertMatchesRegularExpression('/^\\d+$/', $body);
        self::assertSame($body, strip_tags($body));
    }

    #[TestDox('Sequential visits persist and increment the counter exactly once')]
    public function testSequentialPersistence(): void
    {
        $first = $this->displayedCount((string) $this->request('GET')->getBody());
        $second = $this->displayedCount((string) $this->request('GET')->getBody());
        self::assertSame($first + 1, $second);
    }

    #[TestDox('The implementation explicitly protects file access with flock')]
    public function testUsesFileLocking(): void
    {
        SourceAssertions::assertFunctionCallExists($this->sourceDirectory(), 'flock');
    }

    #[TestDox('Concurrent server processes do not lose counter increments')]
    public function testConcurrentRequests(): void
    {
        $baseUrls = [];
        for ($index = 0; $index < 8; $index++) {
            $baseUrls[] = $this->startAdditionalServer();
        }
        $route = ltrim($this->configuration()->route('main'), '/');
        $promises = [];
        foreach ($baseUrls as $baseUrl) {
            $promises[] = (new Client(['http_errors' => false, 'timeout' => 10]))->getAsync($baseUrl . $route);
        }
        $results = Utils::settle($promises)->wait();
        foreach ($results as $result) {
            self::assertSame('fulfilled', $result['state']);
            self::assertSame(200, $result['value']->getStatusCode());
        }
        $final = $this->displayedCount((string) $this->request('GET')->getBody());
        self::assertSame(count($baseUrls) + 1, $final);
    }

    private function displayedCount(string $body): int
    {
        self::assertSame(1, preg_match('/\\d+/', strip_tags($body), $matches));
        return (int) $matches[0];
    }
}
