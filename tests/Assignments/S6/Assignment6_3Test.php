<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S6;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\DatabaseProbe;

#[Group('assignment-6.3')]
#[Group('service')]
final class Assignment6_3Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '6.3';

    #[TestDox('A guestbook entry and its image commit and render as one unit')]
    public function testEntryAndImageCommit(): void
    {
        $image = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true);
        self::assertIsString($image);
        $response = $this->request('POST', 'main', ['multipart' => [
            ['name' => 'name', 'contents' => 'Image Guest'],
            ['name' => 'email', 'contents' => 'image@example.test'],
            ['name' => 'website', 'contents' => 'https://example.test/'],
            ['name' => 'comment', 'contents' => 'Atomic image'],
            ['name' => 'image', 'filename' => 'pixel.png', 'contents' => $image, 'headers' => ['Content-Type' => 'image/png']],
        ]]);
        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('Image Guest', (string) $response->getBody());
        self::assertMatchesRegularExpression('/<img\\b[^>]*src=/i', (string) $response->getBody());
        $imageResponse = $this->request('GET', 'image', ['query' => ['id' => 1]]);
        $this->assertContentType($imageResponse, 'image/png');
        self::assertSame($image, (string) $imageResponse->getBody());
    }

    #[TestDox('A forced image insert failure rolls back the associated entry')]
    public function testTransactionRollback(): void
    {
        $database = DatabaseProbe::connection();
        $entryTable = DatabaseProbe::quotedIdentifier((string) $this->setting('entry_table', 'guestbook'));
        $imageTable = DatabaseProbe::quotedIdentifier((string) $this->setting('image_table', 'images'));
        $before = (int) $database->query("SELECT COUNT(*) FROM {$entryTable}")->fetchColumn();
        $database->exec('DROP TRIGGER IF EXISTS wprog2_force_image_failure');
        $database->exec("CREATE TRIGGER wprog2_force_image_failure BEFORE INSERT ON {$imageTable} FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'forced test failure'");
        try {
            $response = $this->request('POST', 'main', ['multipart' => [
                ['name' => 'name', 'contents' => 'Rollback Guest'],
                ['name' => 'email', 'contents' => 'rollback@example.test'],
                ['name' => 'website', 'contents' => 'https://example.test/'],
                ['name' => 'comment', 'contents' => 'Must roll back'],
                ['name' => 'image', 'filename' => 'failure.png', 'contents' => 'not-important', 'headers' => ['Content-Type' => 'image/png']],
            ]]);
            self::assertGreaterThanOrEqual(400, $response->getStatusCode());
            self::assertSame($before, (int) $database->query("SELECT COUNT(*) FROM {$entryTable}")->fetchColumn());
        } finally {
            $database->exec('DROP TRIGGER IF EXISTS wprog2_force_image_failure');
        }
    }
}
