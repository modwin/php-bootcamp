<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S7;

use DOMXPath;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-7.1')]
#[Group('local')]
final class Assignment7_1Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '7.1';

    #[TestDox('Wiki text can be published as HTML and edited from its original source')]
    public function testPublishAndEdit(): void
    {
        $fields = $this->setting('fields', ['title' => 'title', 'content' => 'content']);
        $wiki = "A plain introduction\n\n== Test heading ==\n\nA *small* marker.";
        $response = $this->request('POST', 'main', ['form_params' => [
            $fields['title'] => 'Fixture post',
            $fields['content'] => $wiki,
        ]]);
        $document = $this->assertValidHtml((string) $response->getBody());
        $xpath = new DOMXPath($document);
        self::assertStringContainsString('Fixture post', $document->textContent);
        self::assertStringContainsString('Test heading', $document->textContent);
        self::assertGreaterThan(0, $xpath->query('//a[contains(@href, "edit")]')->length);
        $edit = $this->request('GET', 'edit', ['query' => ['id' => 1]]);
        self::assertStringContainsString($wiki, html_entity_decode((string) $edit->getBody()));
    }

    #[TestDox('Wiki input cannot inject executable script into generated pages')]
    public function testWikiEscaping(): void
    {
        $fields = $this->setting('fields', ['title' => 'title', 'content' => 'content']);
        $response = $this->request('POST', 'main', ['form_params' => [
            $fields['title'] => 'Unsafe fixture',
            $fields['content'] => '<script>alert(1)</script> ordinary text',
        ]]);
        $document = $this->assertValidHtml((string) $response->getBody());
        self::assertSame(0, (new DOMXPath($document))->query('//script')->length);
        self::assertStringContainsString('ordinary text', $document->textContent);
    }
}
