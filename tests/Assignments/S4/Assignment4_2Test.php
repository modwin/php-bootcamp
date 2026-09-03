<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S4;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\SourceAssertions;

#[Group('assignment-4.2')]
#[Group('local')]
final class Assignment4_2Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '4.2';

    #[TestDox('The custom session cookie lasts approximately three hours')]
    public function testCookieLifetime(): void
    {
        $response = $this->request('GET');
        $header = $response->getHeaderLine('Set-Cookie');
        self::assertNotSame('', $header);
        if (preg_match('/Max-Age=(\\d+)/i', $header, $match) === 1) {
            self::assertEqualsWithDelta(10_800, (int) $match[1], 120);
        } else {
            self::assertSame(1, preg_match('/Expires=([^;]+)/i', $header, $match));
            self::assertEqualsWithDelta(time() + 10_800, strtotime($match[1]), 120);
        }
    }

    #[TestDox('The cookie follows links and forms without built-in sessions')]
    public function testCookieContinuityAndSource(): void
    {
        $this->request('GET');
        $cookies = $this->client()->getConfig('cookies')->toArray();
        self::assertNotEmpty($cookies);
        $response = $this->request('GET', 'echo', ['query' => ['check' => 'cookie']]);
        self::assertStringContainsString((string) $cookies[0]['Value'], (string) $response->getBody());
        $source = '';
        foreach (SourceAssertions::filesWithExtension($this->sourceDirectory(), 'php') as $file) {
            $source .= (string) file_get_contents($file);
        }
        self::assertStringNotContainsString('session_start', $source);
        self::assertStringNotContainsString('$_SESSION', $source);
    }
}
