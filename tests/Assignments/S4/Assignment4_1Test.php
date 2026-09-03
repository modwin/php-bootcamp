<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S4;

use DOMXPath;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-4.1')]
#[Group('local')]
final class Assignment4_1Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '4.1';

    #[TestDox('One session identifier is embedded in every internal link and form')]
    public function testEmbeddedSessionId(): void
    {
        $response = $this->request('GET');
        self::assertFalse($response->hasHeader('Set-Cookie'));
        $document = $this->assertValidHtml((string) $response->getBody());
        $xpath = new DOMXPath($document);
        $ids = [];
        foreach ($xpath->query('//a[@href]') as $link) {
            parse_str((string) parse_url($link->getAttribute('href'), PHP_URL_QUERY), $query);
            self::assertArrayHasKey('session-id', $query);
            $ids[] = $query['session-id'];
        }
        foreach ($xpath->query('//form//input[@type="hidden" and @name="session-id"]') as $input) {
            $ids[] = $input->getAttribute('value');
        }
        self::assertNotEmpty($ids);
        self::assertCount(1, array_unique($ids));
        self::assertGreaterThanOrEqual(16, strlen((string) $ids[0]));
    }

    #[TestDox('Manual link sessions do not use PHP built-in session support')]
    public function testNoBuiltInSessions(): void
    {
        $source = $this->allPhpSource();
        self::assertStringNotContainsString('session_start', $source);
        self::assertStringNotContainsString('$_SESSION', $source);
    }

    private function allPhpSource(): string
    {
        $source = '';
        foreach (\WPROG2\Tests\Support\SourceAssertions::filesWithExtension($this->sourceDirectory(), 'php') as $file) {
            $source .= (string) file_get_contents($file);
        }
        return $source;
    }
}
