<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S8;

use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\ArtifactAssertions;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-8.4')]
#[Group('external')]
final class Assignment8_4Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '8.4';

    #[TestDox('Mutual TLS rejects anonymous clients and accepts a trusted client certificate')]
    public function testClientCertificateAuthentication(): void
    {
        $ca = (string) $this->setting('ca_cert');
        $certificate = (string) $this->setting('client_cert');
        $key = (string) $this->setting('client_key');
        foreach ([$ca, $certificate, $key] as $file) {
            self::assertFileExists($file);
        }
        try {
            $anonymous = $this->request('GET', 'main', ['verify' => $ca]);
            self::assertGreaterThanOrEqual(400, $anonymous->getStatusCode());
        } catch (ConnectException) {
            self::assertTrue(true);
        }
        $authenticated = $this->request('GET', 'main', [
            'verify' => $ca, 'cert' => $certificate, 'ssl_key' => $key,
        ]);
        self::assertSame(200, $authenticated->getStatusCode());
    }

    #[TestDox('Mutual TLS setup and commands are documented')]
    public function testReport(): void
    {
        ArtifactAssertions::assertNonEmptyUtf8Text(ArtifactAssertions::requiredFile($this->configuration(), 'report_file'));
    }
}
