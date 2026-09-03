<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S6;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-6.1')]
#[Group('local')]
final class Assignment6_1Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '6.1';

    #[TestDox('Every visit persists and displays time, address, and user agent')]
    public function testPersistentVisitLog(): void
    {
        $firstAgent = 'WPROG2-Agent-One';
        $secondAgent = 'WPROG2-Agent-Two';
        $this->request('GET', 'main', ['headers' => ['User-Agent' => $firstAgent]]);
        $response = $this->request('GET', 'main', ['headers' => ['User-Agent' => $secondAgent]]);
        $body = (string) $response->getBody();
        self::assertStringContainsString($firstAgent, $body);
        self::assertStringContainsString($secondAgent, $body);
        self::assertMatchesRegularExpression('/127\\.0\\.0\\.1|::1/', $body);
        self::assertMatchesRegularExpression('/\\d{4}[-\\/]\\d{2}[-\\/]\\d{2}|\\d{2}:\\d{2}/', $body);
    }
}
