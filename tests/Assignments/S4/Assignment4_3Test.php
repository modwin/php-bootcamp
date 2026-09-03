<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S4;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\SourceAssertions;

#[Group('assignment-4.3')]
#[Group('local')]
final class Assignment4_3Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '4.3';

    #[TestDox('PHP built-in sessions preserve input and isolate separate clients')]
    public function testBuiltInSessions(): void
    {
        $source = '';
        foreach (SourceAssertions::filesWithExtension($this->sourceDirectory(), 'php') as $file) {
            $source .= (string) file_get_contents($file);
        }
        self::assertStringContainsString('session_start', $source);

        $this->request('GET');
        $firstCookies = $this->client()->getConfig('cookies')->toArray();
        self::assertNotEmpty($firstCookies);

        $baseUri = (string) $this->client()->getConfig('base_uri');
        $second = new Client(['base_uri' => $baseUri, 'cookies' => new CookieJar(), 'http_errors' => false]);
        $second->get($this->configuration()->route('main'));
        $secondCookies = $second->getConfig('cookies')->toArray();
        self::assertNotEmpty($secondCookies);
        self::assertNotSame($firstCookies[0]['Value'], $secondCookies[0]['Value']);

        $response = $this->request('POST', 'echo', ['form_params' => ['message' => 'session-value']]);
        self::assertStringContainsString('session-value', (string) $response->getBody());
    }
}
