<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S5;

use DOMXPath;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\MailProbe;

#[Group('assignment-5.1')]
#[Group('service')]
final class Assignment5_1Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '5.1';

    #[TestDox('The mail form exposes every required address and message field')]
    public function testFormFields(): void
    {
        $document = $this->assertValidHtml((string) $this->request('GET', 'form')->getBody());
        $xpath = new DOMXPath($document);
        foreach (['from', 'to', 'cc', 'bcc', 'subject', 'message'] as $field) {
            self::assertGreaterThan(0, $xpath->query("//*[@name='{$field}']")->length, "Missing {$field} field.");
        }
    }

    #[TestDox('A submitted message reaches the sandbox with headers, body, and warning footer')]
    public function testMailDelivery(): void
    {
        $mail = MailProbe::fromEnvironment();
        $mail->purge();
        $response = $this->request('POST', 'main', ['form_params' => [
            'from' => 'sender@test.local', 'to' => 'student@test.local', 'cc' => 'cc@test.local',
            'bcc' => 'bcc@test.local', 'subject' => 'WPROG2 subject', 'message' => 'WPROG2 body',
        ]]);
        self::assertSame(200, $response->getStatusCode());
        self::assertMatchesRegularExpression('/sent|skick/i', (string) $response->getBody());
        $messages = $mail->messages();
        self::assertCount(1, $messages);
        foreach (['sender@test.local', 'student@test.local', 'cc@test.local', 'WPROG2 subject', 'WPROG2 body'] as $value) {
            self::assertStringContainsString($value, $messages[0]);
        }
        self::assertStringContainsString('Observera!', $messages[0]);
        self::assertStringContainsString('avsändaren kan vara felaktig', $messages[0]);
    }
}
