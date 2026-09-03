<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S8;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\ArtifactAssertions;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-8.1')]
#[Group('external')]
#[Group('provisional')]
final class Assignment8_1Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '8.1';

    #[TestDox('Basic authentication rejects missing and wrong credentials and displays the authenticated username')]
    public function testBasicAuthentication(): void
    {
        $username = (string) $this->setting('username', 'student');
        $password = (string) $this->setting('password', 'test-only-password');
        $anonymous = $this->request('GET');
        self::assertSame(401, $anonymous->getStatusCode());
        self::assertStringStartsWith('Basic ', $anonymous->getHeaderLine('WWW-Authenticate'));
        self::assertSame(401, $this->request('GET', 'main', ['auth' => [$username, 'wrong', 'basic']])->getStatusCode());
        $authenticated = $this->request('GET', 'main', ['auth' => [$username, $password, 'basic']]);
        self::assertSame(200, $authenticated->getStatusCode());
        self::assertStringContainsString($username, (string) $authenticated->getBody());
    }

    #[TestDox('The authentication setup commands are documented')]
    public function testReport(): void
    {
        ArtifactAssertions::assertNonEmptyUtf8Text(ArtifactAssertions::requiredFile($this->configuration(), 'report_file'));
    }
}
