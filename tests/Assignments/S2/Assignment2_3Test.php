<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S2;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-2.3')]
#[Group('local')]
final class Assignment2_3Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '2.3';

    #[TestDox('Allowed text, JPEG, and PNG uploads return exact bytes and MIME')]
    public function testAllowedUploads(): void
    {
        $fixtures = [
            ['notes.txt', 'text/plain', "Hej UTF-8: räksmörgås\n"],
            ['pixel.png', 'image/png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true)],
            ['pixel.jpg', 'image/jpeg', base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAF//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABBQJ//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAwEBPwF//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAgEBPwF//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQAGPwJ//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPyF//9k=', true)],
        ];
        foreach ($fixtures as [$filename, $mime, $contents]) {
            self::assertIsString($contents);
            $response = $this->request('POST', 'main', ['multipart' => [[
                'name' => 'upload', 'filename' => $filename, 'contents' => $contents, 'headers' => ['Content-Type' => $mime],
            ]]]);
            $this->assertContentType($response, $mime);
            self::assertSame($contents, (string) $response->getBody());
        }
    }

    #[TestDox('Unsupported uploads disclose only basename, detected type, and size')]
    public function testUnsupportedUploadMetadata(): void
    {
        $contents = '%PDF-1.4 test fixture';
        $response = $this->request('POST', 'main', ['multipart' => [[
            'name' => 'upload', 'filename' => '../report.pdf', 'contents' => $contents, 'headers' => ['Content-Type' => 'application/pdf'],
        ]]]);
        $body = (string) $response->getBody();
        self::assertStringContainsString('report.pdf', $body);
        self::assertStringNotContainsString('../', $body);
        self::assertStringContainsString((string) strlen($contents), $body);
        self::assertMatchesRegularExpression('/application\\/pdf|application\\/octet-stream/i', $body);
    }

    #[TestDox('An upload above the declared limit is rejected without echoing its body')]
    public function testOversizeUpload(): void
    {
        $limit = (int) $this->setting('max_upload_bytes', 1_048_576);
        $contents = str_repeat('X', $limit + 1);
        $response = $this->request('POST', 'main', ['multipart' => [[
            'name' => 'upload', 'filename' => 'too-large.txt', 'contents' => $contents, 'headers' => ['Content-Type' => 'text/plain'],
        ]]]);
        self::assertGreaterThanOrEqual(400, $response->getStatusCode());
        self::assertStringNotContainsString($contents, (string) $response->getBody());
    }
}
