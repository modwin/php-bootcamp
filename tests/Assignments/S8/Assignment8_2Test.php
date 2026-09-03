<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S8;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\ArtifactAssertions;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-8.2')]
#[Group('external')]
final class Assignment8_2Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '8.2';

    #[TestDox('Digest authentication challenges clients and accepts only the correct digest')]
    public function testDigestAuthentication(): void
    {
        $username = (string) $this->setting('username', 'student');
        $password = (string) $this->setting('password', 'test-only-password');
        $anonymous = $this->request('GET');
        self::assertSame(401, $anonymous->getStatusCode());
        self::assertStringStartsWith('Digest ', $anonymous->getHeaderLine('WWW-Authenticate'));
        self::assertStringNotContainsString($password, $anonymous->getHeaderLine('WWW-Authenticate'));
        self::assertSame(401, $this->request('GET', 'main', ['auth' => [$username, 'wrong', 'digest']])->getStatusCode());
        $authenticated = $this->request('GET', 'main', ['auth' => [$username, $password, 'digest']]);
        self::assertSame(200, $authenticated->getStatusCode());
        self::assertStringContainsString($username, (string) $authenticated->getBody());
    }

    #[TestDox('Digest setup and commands are documented')]
    public function testReport(): void
    {
        $contents = ArtifactAssertions::assertNonEmptyUtf8Text(ArtifactAssertions::requiredFile($this->configuration(), 'report_file'));
        self::assertMatchesRegularExpression('/digest|htdigest|authdigest/i', $contents);
    }
}
