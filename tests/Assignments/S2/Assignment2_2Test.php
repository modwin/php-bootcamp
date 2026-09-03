<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S2;

use DOMXPath;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-2.2')]
#[Group('local')]
final class Assignment2_2Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '2.2';

    #[TestDox('The HTML form exposes several named control types and a server action')]
    public function testFormStructure(): void
    {
        $document = $this->assertValidHtml((string) $this->request('GET', 'form')->getBody());
        $xpath = new DOMXPath($document);
        self::assertGreaterThanOrEqual(3, $xpath->query('//form//*[@name]')->length);
        self::assertGreaterThan(0, $xpath->query('//form[@action]')->length);
    }

    #[TestDox('GET and POST return equivalent arbitrary name/value pairs as plain text')]
    public function testGetAndPostRoundTrip(): void
    {
        $values = ['person' => 'Ada Lovelace', 'language' => 'PHP', 'unicode' => 'räksmörgås'];
        foreach ([['GET', 'query'], ['POST', 'form_params']] as [$method, $option]) {
            $response = $this->request($method, 'main', [$option => $values]);
            $this->assertContentType($response, 'text/plain');
            $body = (string) $response->getBody();
            foreach ($values as $name => $value) {
                self::assertStringContainsString($name, $body);
                self::assertStringContainsString($value, $body);
            }
        }
    }
}
