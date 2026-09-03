<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S8;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-8.5')]
#[Group('external')]
final class Assignment8_5Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '8.5';

    #[TestDox('The three-hour session cookie is marked Secure and survives HTTPS requests')]
    public function testSecureCookie(): void
    {
        $ca = (string) $this->setting('ca_cert');
        $response = $this->request('GET', 'main', ['verify' => $ca]);
        $header = $response->getHeaderLine('Set-Cookie');
        self::assertMatchesRegularExpression('/;\\s*Secure(?:;|$)/i', $header);
        if (preg_match('/Max-Age=(\\d+)/i', $header, $match) === 1) {
            self::assertEqualsWithDelta(10_800, (int) $match[1], 120);
        }
        $cookies = $this->client()->getConfig('cookies')->toArray();
        self::assertNotEmpty($cookies);
        self::assertSame(200, $this->request('GET', 'echo', ['verify' => $ca])->getStatusCode());
    }

    #[TestDox('The secure cookie is not sent to the configured plain HTTP endpoint')]
    public function testCookieNotSentOverHttp(): void
    {
        $ca = (string) $this->setting('ca_cert');
        $this->request('GET', 'main', ['verify' => $ca]);
        $cookies = $this->client()->getConfig('cookies')->toArray();
        self::assertNotEmpty($cookies);
        $httpUrl = (string) $this->setting('http_url');
        self::assertStringStartsWith('http://', $httpUrl);
        $response = $this->client()->get($httpUrl, ['http_errors' => false]);
        self::assertStringNotContainsString((string) $cookies[0]['Value'], (string) $response->getBody());
    }
}
