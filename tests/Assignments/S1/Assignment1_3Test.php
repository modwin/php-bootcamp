<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S1;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-1.3')]
#[Group('local')]
#[Group('provisional')]
final class Assignment1_3Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '1.3';

    #[TestDox('The graphics endpoint returns declared, decodable image bytes')]
    public function testGeneratedImage(): void
    {
        $response = $this->request('GET');
        self::assertSame(200, $response->getStatusCode());
        self::assertMatchesRegularExpression('~^image/(?:png|jpeg|gif)~i', $response->getHeaderLine('Content-Type'));
        $image = @getimagesizefromstring((string) $response->getBody());
        self::assertIsArray($image);
        self::assertGreaterThan(0, $image[0]);
        self::assertGreaterThan(0, $image[1]);
    }
}
