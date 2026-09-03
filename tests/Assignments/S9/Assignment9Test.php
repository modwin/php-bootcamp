<?php

declare(strict_types=1);

namespace WPROG2\Tests\Assignments\S9;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\Process\Process;
use WPROG2\Tests\Support\ArtifactAssertions;
use WPROG2\Tests\Support\AssignmentTestCase;

#[Group('assignment-9')]
#[Group('local')]
#[Group('provisional')]
final class Assignment9Test extends AssignmentTestCase
{
    protected const ASSIGNMENT_ID = '9';

    #[TestDox('The capstone application has a working main route and declares reused techniques')]
    public function testApplicationAndTechniques(): void
    {
        $response = $this->request('GET');
        self::assertSame(200, $response->getStatusCode());
        self::assertNotSame('', trim((string) $response->getBody()));
        $techniques = $this->setting('techniques', []);
        self::assertIsArray($techniques);
        self::assertNotEmpty($techniques);
        foreach ($techniques as $id) {
            self::assertMatchesRegularExpression('/^[1-8]\\.[1-6]$/', (string) $id);
        }
    }

    #[TestDox('Beskrivning.pdf is readable and contains the required report sections')]
    public function testCapstonePdf(): void
    {
        $pdf = ArtifactAssertions::requiredFile($this->configuration(), 'pdf_file');
        $header = file_get_contents($pdf, false, null, 0, 5);
        self::assertSame('%PDF-', $header);

        $info = new Process(['pdfinfo', $pdf]);
        $info->run();
        if (!$info->isSuccessful()) {
            self::markTestSkipped('Install Poppler or run the Docker test runner to inspect PDF structure.');
        }
        self::assertSame(1, preg_match('/Pages:\s+(\\d+)/i', $info->getOutput(), $match));
        self::assertGreaterThanOrEqual(6, (int) $match[1]);

        $text = new Process(['pdftotext', '-layout', $pdf, '-']);
        $text->mustRun();
        foreach (['Sammanfattning', 'Framtagning', 'Konstruktion', 'Funktion'] as $heading) {
            self::assertStringContainsStringIgnoringCase($heading, $text->getOutput());
        }
    }
}
