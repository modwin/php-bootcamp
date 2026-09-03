<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S1;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-1.4')]
#[Group('local')]
#[Group('provisional')]
final class Assignment1_4Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '1.4';

    #[TestDox('The response implements client pull with a valid HTTP Refresh header')]
    public function testRefreshHeader(): void
    {
        $response = $this->request('GET');
        self::assertSame(200, $response->getStatusCode());
        $refresh = $response->getHeaderLine('Refresh');
        self::assertMatchesRegularExpression('/^\\s*\\d+(?:\\.\\d+)?\\s*;\\s*url=\\S+/i', $refresh);
        self::assertFalse($response->hasHeader('Location'), 'Client pull should not be replaced by an HTTP redirect.');
    }
}
