<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S1;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-1.2')]
#[Group('local')]
final class Assignment1_2Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '1.2';

    #[TestDox('Every environment entry is returned as a plain-text name/value line')]
    public function testEnvironmentListing(): void
    {
        $agent = 'WPROG2-Test-Agent/1.0';
        $response = $this->request('GET', 'main', ['headers' => ['User-Agent' => $agent]]);
        self::assertSame(200, $response->getStatusCode());
        $this->assertContentType($response, 'text/plain');
        $body = (string) $response->getBody();
        self::assertStringContainsString('HTTP_USER_AGENT', $body);
        self::assertStringContainsString($agent, $body);
        self::assertStringContainsString('REMOTE_ADDR', $body);
        self::assertSame($body, strip_tags($body));
        foreach (array_filter(preg_split('/\\R/', trim($body)) ?: []) as $line) {
            self::assertMatchesRegularExpression('/^\\S+\\s*[:=]\\s*.*$/u', $line);
        }
    }
}
