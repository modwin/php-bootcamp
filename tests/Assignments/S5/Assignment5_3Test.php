<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S5;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;
use WPROG2\Tests\Support\MailProbe;

#[Group('assignment-5.3')]
#[Group('service')]
#[Group('provisional')]
final class Assignment5_3Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '5.3';

    #[TestDox('A seeded plain message can be listed and read')]
    public function testReceivePlainMessage(): void
    {
        $mail = MailProbe::fromEnvironment();
        $mail->purge();
        $mail->sendFixture('Fixture subject', 'Fixture body');
        $body = (string) $this->request('GET')->getBody();
        self::assertStringContainsString('Fixture subject', $body);
        self::assertStringContainsString('Fixture body', $body);
        self::assertStringContainsString('fixture@test.local', $body);
    }
}
