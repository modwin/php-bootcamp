<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S5;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\MailProbe;

#[Group('assignment-5.4')]
#[Group('service')]
#[Group('provisional')]
final class Assignment5_4Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '5.4';

    #[TestDox('A received attachment exposes safe metadata and exact downloadable bytes')]
    public function testReceiveAttachment(): void
    {
        $attachment = random_bytes(48);
        $mail = MailProbe::fromEnvironment();
        $mail->purge();
        $mail->sendFixture('Attachment fixture', 'Download it', $attachment);
        $listing = (string) $this->request('GET')->getBody();
        self::assertStringContainsString('fixture.bin', $listing);
        $download = $this->request('GET', 'attachment', ['query' => ['message' => 1, 'attachment' => 1]]);
        self::assertSame($attachment, (string) $download->getBody());
        self::assertStringNotContainsString('../', $download->getHeaderLine('Content-Disposition'));
    }
}
