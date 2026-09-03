<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S8;

use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\ArtifactAssertions;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-8.3')]
#[Group('external')]
final class Assignment8_3Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '8.3';

    #[TestDox('HTTPS succeeds with its test CA and fails without trusted server authentication')]
    public function testServerCertificate(): void
    {
        $ca = (string) $this->setting('ca_cert');
        self::assertFileExists($ca);
        self::assertSame(200, $this->request('GET', 'main', ['verify' => $ca])->getStatusCode());
        try {
            $this->request('GET', 'main', ['verify' => true]);
            self::fail('The test CA was unexpectedly trusted by the system trust store.');
        } catch (ConnectException) {
            self::assertTrue(true);
        }
    }

    #[TestDox('HTTPS server setup and commands are documented')]
    public function testReport(): void
    {
        ArtifactAssertions::assertNonEmptyUtf8Text(ArtifactAssertions::requiredFile($this->configuration(), 'report_file'));
    }
}
