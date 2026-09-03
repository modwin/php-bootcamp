<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S6;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\ArtifactAssertions;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\DatabaseProbe;
use WPROG2\Tests\Support\SourceAssertions;

#[Group('assignment-6.4')]
#[Group('service')]
final class Assignment6_4Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '6.4';

    #[TestDox('The configured MariaDB stored procedure exists and the application calls it')]
    public function testStoredProcedure(): void
    {
        $procedure = (string) $this->setting('stored_procedure');
        self::assertMatchesRegularExpression('/^[A-Za-z_][A-Za-z0-9_]*$/', $procedure);
        $database = DatabaseProbe::connection();
        $statement = $database->prepare("SELECT COUNT(*) FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = ? AND ROUTINE_TYPE = 'PROCEDURE'");
        $statement->execute([$procedure]);
        self::assertSame(1, (int) $statement->fetchColumn());
        $source = '';
        foreach (SourceAssertions::filesWithExtension($this->sourceDirectory(), 'php') as $file) {
            $source .= (string) file_get_contents($file);
        }
        self::assertMatchesRegularExpression('/\\bCALL\\s+' . preg_quote($procedure, '/') . '\\b/i', $source);
        self::assertMatchesRegularExpression('/ATTR_PERSISTENT|mysqli_connect\\s*\\(\\s*[\'\"]p:/i', $source);
    }

    #[TestDox('A detailed UTF-8 implementation report is included')]
    public function testReportArtifact(): void
    {
        $report = ArtifactAssertions::requiredFile($this->configuration(), 'report_file');
        $contents = ArtifactAssertions::assertNonEmptyUtf8Text($report);
        self::assertMatchesRegularExpression('/stored procedure|lagrad procedur/i', $contents);
        self::assertMatchesRegularExpression('/connection pool|anslutningspool/i', $contents);
    }
}
