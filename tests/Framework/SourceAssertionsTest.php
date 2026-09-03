<?php

declare(strict_types=1);

namespace WPROG2\Tests\Framework;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WPROG2\Tests\Support\SourceAssertions;

final class SourceAssertionsTest extends TestCase
{
    #[TestDox('Source assertions recognize separated HTML and PHP fixtures')]
    public function testSeparatedFixture(): void
    {
        $root = WPROG2_ROOT . '/tests/Fixtures/source';
        SourceAssertions::assertHtmlFilesContainNoPhp($root);
        SourceAssertions::assertPhpFilesContainNoStructuralHtml($root);
        $this->addToAssertionCount(1);
    }
}
