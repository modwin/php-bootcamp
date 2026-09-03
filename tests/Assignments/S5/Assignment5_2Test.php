<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S5;

use DOMXPath;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\MailProbe;

#[Group('assignment-5.2')]
#[Group('service')]
final class Assignment5_2Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '5.2';

    #[TestDox('The form accepts at least two file attachments')]
    public function testAttachmentInputs(): void
    {
        $document = $this->assertValidHtml((string) $this->request('GET', 'form')->getBody());
        $xpath = new DOMXPath($document);
        self::assertGreaterThanOrEqual(2, $xpath->query('//input[@type="file"]')->length);
    }

    #[TestDox('Two arbitrary attachments arrive with names and exact encoded bytes')]
    public function testAttachmentDelivery(): void
    {
        $mail = MailProbe::fromEnvironment();
        $mail->purge();
        $first = random_bytes(32);
        $second = "UTF-8 attachment: räksmörgås\n";
        $multipart = [
            ['name' => 'from', 'contents' => 'sender@test.local'],
            ['name' => 'to', 'contents' => 'student@test.local'],
            ['name' => 'subject', 'contents' => 'Attachments'],
            ['name' => 'message', 'contents' => 'Attached files'],
            ['name' => 'attachment1', 'filename' => 'first.bin', 'contents' => $first],
            ['name' => 'attachment2', 'filename' => 'second.txt', 'contents' => $second],
        ];
        self::assertSame(200, $this->request('POST', 'main', ['multipart' => $multipart])->getStatusCode());
        $messages = $mail->messages();
        self::assertCount(1, $messages);
        foreach (['first.bin', 'second.txt', base64_encode($first), base64_encode($second)] as $value) {
            self::assertStringContainsString($value, str_replace(["\r", "\n"], '', $messages[0]));
        }
    }
}
