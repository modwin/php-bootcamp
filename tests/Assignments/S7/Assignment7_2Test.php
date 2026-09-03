<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S7;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-7.2')]
#[Group('local')]
#[Group('provisional')]
final class Assignment7_2Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '7.2';

    #[TestDox('The syndication endpoint emits well-formed UTF-8 feed metadata and items')]
    public function testSyndicationFeed(): void
    {
        $response = $this->request('GET', 'feed');
        self::assertMatchesRegularExpression('~^(application|text)/(rss\\+xml|atom\\+xml|xml)~i', $response->getHeaderLine('Content-Type'));
        $xml = simplexml_load_string((string) $response->getBody());
        self::assertNotFalse($xml);
        $names = array_map('strtolower', array_keys($xml->getNamespaces(true)));
        $serialized = strtolower((string) $response->getBody());
        self::assertTrue(str_contains($serialized, '<rss') || str_contains($serialized, '<feed'));
        self::assertMatchesRegularExpression('/<title>.+<\\/title>/is', $serialized);
        self::assertMatchesRegularExpression('/<(?:item|entry)\\b/is', $serialized);
    }
}
