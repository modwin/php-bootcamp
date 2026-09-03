<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S2;

use DOMXPath;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-2.1')]
#[Group('local')]
final class Assignment2_1Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '2.1';

    #[TestDox('The HTML document contains a link carrying multiple query parameters')]
    public function testQueryLinkExists(): void
    {
        $response = $this->request('GET', 'page');
        $document = $this->assertValidHtml((string) $response->getBody());
        $links = (new DOMXPath($document))->query('//a[contains(@href, "?") and contains(@href, "=")]');
        self::assertNotFalse($links);
        self::assertGreaterThan(0, $links->length);
    }

    #[TestDox('Arbitrary GET pairs round-trip as UTF-8 plain text')]
    public function testGetRoundTrip(): void
    {
        $values = ['name' => 'Björn & Ada', 'empty' => '', 'symbol' => 'a=b+c'];
        $response = $this->request('GET', 'main', ['query' => $values]);
        $this->assertContentType($response, 'text/plain');
        $body = (string) $response->getBody();
        foreach ($values as $name => $value) {
            self::assertStringContainsString($name, $body);
            self::assertStringContainsString($value, $body);
        }
        self::assertSame($body, strip_tags($body));
    }
}
