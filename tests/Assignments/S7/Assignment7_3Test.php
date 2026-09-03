<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S7;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-7.3')]
#[Group('local')]
final class Assignment7_3Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '7.3';

    #[TestDox('Users can add and remove a local feed source and aggregate its items on demand')]
    public function testSourceManagementAndAggregation(): void
    {
        $fixture = $this->startFixtureServer(WPROG2_ROOT . '/tests/Fixtures/web/public');
        $feed = $fixture . 'feed.rss';
        $this->request('POST', 'sources', ['form_params' => ['action' => 'add', 'url' => $feed]]);
        $body = (string) $this->request('GET', 'main')->getBody();
        self::assertStringContainsString('Fixture Article One', $body);
        self::assertStringContainsString('Fixture Article Two', $body);
        $this->request('POST', 'sources', ['form_params' => ['action' => 'remove', 'url' => $feed]]);
        self::assertStringNotContainsString('Fixture Article One', (string) $this->request('GET', 'main')->getBody());
    }

    #[TestDox('Malformed and unavailable feeds produce a controlled response')]
    public function testBadSources(): void
    {
        $fixture = $this->startFixtureServer(WPROG2_ROOT . '/tests/Fixtures/web/public');
        foreach ([$fixture . 'malformed.xml', 'http://127.0.0.1:1/unavailable'] as $url) {
            $this->request('POST', 'sources', ['form_params' => ['action' => 'add', 'url' => $url]]);
        }
        $response = $this->request('GET', 'main');
        self::assertSame(200, $response->getStatusCode());
        self::assertMatchesRegularExpression('/unavailable|invalid|kunde inte|fel/i', (string) $response->getBody());
    }
}
