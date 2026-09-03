<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S3;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\SourceAssertions;

#[Group('assignment-3.1')]
#[Group('local')]
final class Assignment3_1Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '3.1';

    #[TestDox('The counter renders complete HTML with every fixed marker replaced')]
    public function testRenderedCounter(): void
    {
        $response = $this->request('GET');
        $this->assertContentType($response, 'text/html');
        $body = (string) $response->getBody();
        $this->assertValidHtml($body);
        self::assertMatchesRegularExpression('/\\d+/', strip_tags($body));
        self::assertDoesNotMatchRegularExpression('/---[^-]+---/', $body);
    }

    #[TestDox('HTML and PHP remain separate while counter access uses locking')]
    public function testSourceSeparationAndLocking(): void
    {
        SourceAssertions::assertHtmlFilesContainNoPhp($this->sourceDirectory());
        SourceAssertions::assertPhpFilesContainNoStructuralHtml($this->sourceDirectory());
        SourceAssertions::assertFunctionCallExists($this->sourceDirectory(), 'flock');
    }
}
