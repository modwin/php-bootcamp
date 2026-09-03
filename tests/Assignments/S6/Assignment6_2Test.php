<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S6;

use PDO;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\DatabaseProbe;

#[Group('assignment-6.2')]
#[Group('service')]
final class Assignment6_2Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '6.2';

    #[TestDox('A guestbook entry is timestamped, persisted, escaped, and linked safely')]
    public function testGuestbookEntry(): void
    {
        $fields = $this->fields();
        $payload = [
            $fields['name'] => '<script>alert(1)</script>Ada',
            $fields['email'] => 'ada@example.test',
            $fields['website'] => 'https://example.test/',
            $fields['comment'] => 'Hello guestbook',
        ];
        $response = $this->request('POST', 'main', ['form_params' => $payload]);
        $body = (string) $response->getBody();
        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('Ada', $body);
        self::assertStringContainsString('mailto:ada@example.test', $body);
        self::assertStringContainsString('https://example.test/', $body);
        self::assertStringNotContainsString('<script>', $body);
        self::assertMatchesRegularExpression('/\\d{4}[-\\/]\\d{2}[-\\/]\\d{2}|\\d{2}:\\d{2}/', $body);
    }

    #[TestDox('SQL injection is stored as data and cannot damage the guestbook table')]
    public function testSqlInjection(): void
    {
        $table = DatabaseProbe::quotedIdentifier((string) $this->setting('entry_table', 'guestbook'));
        $database = DatabaseProbe::connection();
        $before = (int) $database->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
        $fields = $this->fields();
        $attack = "Robert'); DROP TABLE guestbook;--";
        $this->request('POST', 'main', ['form_params' => [
            $fields['name'] => $attack,
            $fields['email'] => 'robert@example.test',
            $fields['website'] => 'https://example.test/',
            $fields['comment'] => '<b>must not execute</b>',
        ]]);
        $after = (int) $database->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
        self::assertSame($before + 1, $after);
        self::assertSame('BASE TABLE', $database->query(
            "SELECT TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "
            . $database->quote(trim($table, '`')),
        )->fetchColumn());
    }

    /** @return array{name: string, email: string, website: string, comment: string} */
    private function fields(): array
    {
        return $this->setting('fields', [
            'name' => 'name', 'email' => 'email', 'website' => 'website', 'comment' => 'comment',
        ]);
    }
}
