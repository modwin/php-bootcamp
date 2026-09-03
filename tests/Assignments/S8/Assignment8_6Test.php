<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S8;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\ArtifactAssertions;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-8.6')]
#[Group('local')]
final class Assignment8_6Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '8.6';

    #[TestDox('The UTF-8 report compares multiple payment approaches and their security')]
    public function testPaymentReport(): void
    {
        $contents = ArtifactAssertions::assertNonEmptyUtf8Text(
            ArtifactAssertions::requiredFile($this->configuration(), 'report_file'),
        );
        self::assertMatchesRegularExpression('/payment|betalning/i', $contents);
        self::assertMatchesRegularExpression('/security|säkerhet|risk/i', $contents);
        self::assertGreaterThanOrEqual(2, preg_match_all('/^#{1,3}\\s+|^\\S.+:\s*$/m', $contents));
    }
}
