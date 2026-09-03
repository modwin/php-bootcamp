<?php

declare(strict_types=1);

namespace WPROG2\Tests\Framework;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WPROG2\Tests\Support\AssignmentRegistry;

final class AssignmentRegistryTest extends TestCase
{
    #[TestDox('The registry covers every course assignment exactly once')]
    public function testRegistryCoverage(): void
    {
        $expected = [
            '1.1', '1.2', '1.3', '1.4', '2.1', '2.2', '2.3', '3.1', '3.2',
            '4.1', '4.2', '4.3', '5.1', '5.2', '5.3', '5.4', '6.1', '6.2',
            '6.3', '6.4', '7.1', '7.2', '7.3', '7.4', '8.1', '8.2', '8.3',
            '8.4', '8.5', '8.6', '9',
        ];
        self::assertSame($expected, AssignmentRegistry::ids());
        self::assertCount(31, array_unique(AssignmentRegistry::ids()));
    }

    #[TestDox('Only definitions absent from the supplied text are provisional')]
    public function testProvisionalAssignments(): void
    {
        $actual = array_map('strval', array_keys(array_filter(
            AssignmentRegistry::all(),
            static fn (array $entry): bool => $entry['provisional'],
        )));
        self::assertSame(['1.3', '1.4', '5.3', '5.4', '7.2', '8.1', '9'], $actual);
    }
}
