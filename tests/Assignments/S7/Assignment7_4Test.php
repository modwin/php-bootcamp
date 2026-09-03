<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S7;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-7.4')]
#[Group('local')]
final class Assignment7_4Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '7.4';

    #[TestDox('The crawler finds local matches, resolves relative links, and terminates cycles')]
    public function testDeterministicCrawl(): void
    {
        $fixture = $this->startFixtureServer(WPROG2_ROOT . '/tests/Fixtures/web/public');
        $response = $this->request('GET', 'main', ['query' => [
            'url' => $fixture . 'crawl/start.html', 'term' => 'WPROG2 needle', 'depth' => 2,
        ]]);
        $body = (string) $response->getBody();
        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('start.html', $body);
        self::assertStringContainsString('nested/b.html', $body);
        self::assertLessThanOrEqual(1, substr_count($body, 'crawl/start.html'), 'A cycle caused the start page to be reported repeatedly.');
    }

    #[TestDox('The configured maximum depth prevents deeper pages from being visited')]
    public function testDepthLimit(): void
    {
        $fixture = $this->startFixtureServer(WPROG2_ROOT . '/tests/Fixtures/web/public');
        $body = (string) $this->request('GET', 'main', ['query' => [
            'url' => $fixture . 'crawl/start.html', 'term' => 'Depth-two terminal page', 'depth' => 1,
        ]])->getBody();
        self::assertStringNotContainsString('nested/c.html', $body);
        self::assertStringNotContainsString('Depth-two terminal page', $body);
    }
}
