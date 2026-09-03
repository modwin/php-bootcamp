<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S3;

use DOMXPath;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\SourceAssertions;

#[Group('assignment-3.2')]
#[Group('local')]
final class Assignment3_2Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '3.2';

    #[TestDox('Variable environment entries become escaped repeated HTML rows')]
    public function testVariableRows(): void
    {
        $payload = '<script>alert(1)</script>';
        $response = $this->request('GET', 'main', ['headers' => ['User-Agent' => $payload]]);
        $document = $this->assertValidHtml((string) $response->getBody());
        $xpath = new DOMXPath($document);
        self::assertGreaterThan(1, $xpath->query('//tr')->length);
        self::assertSame(0, $xpath->query('//script')->length);
        self::assertStringContainsString($payload, $document->textContent);
        self::assertDoesNotMatchRegularExpression('/---[^-]+---|<!--=+[^>]+=+-->/', (string) $response->getBody());
    }

    #[TestDox('Variable output uses separate pure HTML and server-code files')]
    public function testSourceSeparation(): void
    {
        SourceAssertions::assertHtmlFilesContainNoPhp($this->sourceDirectory());
        SourceAssertions::assertPhpFilesContainNoStructuralHtml($this->sourceDirectory());
    }
}
