<?php

declare(strict_types=1);

namespace WPROG2\Tests\Framework;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WPROG2\Tests\Support\AssignmentRegistry;
use WPROG2\Tests\Support\AssignmentTestCase;

final class TestClassCoverageTest extends TestCase
{
    #[TestDox('Every registry entry has one loadable assignment test class')]
    public function testEveryAssignmentHasATestClass(): void
    {
        foreach (AssignmentRegistry::all() as $id => $entry) {
            self::assertTrue(class_exists($entry['class']), "Missing test class for assignment {$id}: {$entry['class']}");
            $reflection = new ReflectionClass($entry['class']);
            self::assertTrue($reflection->isSubclassOf(AssignmentTestCase::class));
            $groups = array_map(
                static fn ($attribute): string => $attribute->newInstance()->name(),
                $reflection->getAttributes(Group::class),
            );
            self::assertContains('assignment-' . $id, $groups, "Missing assignment group on {$entry['class']}");
        }
    }
}
