<?php

declare(strict_types=1);

namespace WPROG2\S6_Databases\Ex6_2_Database_Security\public;

use RuntimeException;

final class BlockTemplateEngine
{
    private string $template;

    public function __construct(string $filePath)
    {
        $template = file_get_contents($filePath);
        if ($template === false) {
            throw new RuntimeException("Could not read template '{$filePath}'.");
        }

        $this->template = $template;
    }

    /**
     * @param array<string, list<array<string, scalar|null>>> $blocks
     * @param array<string, scalar|null> $variables
     */
    public function render(array $blocks, array $variables = []): string
    {
        $output = $this->template;
        $protectedValues = [];

        foreach ($blocks as $blockName => $rows) {
            $this->assertValidName($blockName, 'block');
            $marker = "<!--==={$blockName}===-->";

            if (substr_count($output, $marker) !== 2) {
                throw new RuntimeException("Template block '{$blockName}' must have exactly two markers.");
            }

            [$before, $rowTemplate, $after] = explode($marker, $output, 3);
            $renderedRows = '';

            foreach ($rows as $row) {
                if (!is_array($row)) {
                    throw new RuntimeException("Rows in template block '{$blockName}' must be arrays.");
                }

                $renderedRows .= $this->replaceVariables($rowTemplate, $row, $protectedValues);
            }

            $output = $before . $renderedRows . $after;
        }

        $output = $this->replaceVariables($output, $variables, $protectedValues);

        if (preg_match('/<!--===[A-Za-z_][A-Za-z0-9_]*===-->/', $output) === 1) {
            throw new RuntimeException('The template contains an unrendered block.');
        }

        if (preg_match('/---[A-Za-z_][A-Za-z0-9_]*---/', $output) === 1) {
            throw new RuntimeException('The template contains an unresolved variable.');
        }

        return strtr($output, $protectedValues);
    }

    /**
     * @param array<string, scalar|null> $variables
     * @param array<string, string> $protectedValues
     */
    private function replaceVariables(string $template, array $variables, array &$protectedValues): string
    {
        foreach ($variables as $name => $value) {
            $this->assertValidName($name, 'variable');
            if (!is_scalar($value) && $value !== null) {
                throw new RuntimeException("Template variable '{$name}' must be scalar or null.");
            }

            $escapedValue = htmlspecialchars(
                (string) $value,
                ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5,
                'UTF-8',
            );
            $token = "\x1A" . count($protectedValues) . "\x1A";
            $protectedValues[$token] = $escapedValue;
            $template = str_replace("---{$name}---", $token, $template);
        }

        return $template;
    }

    private function assertValidName(mixed $name, string $kind): void
    {
        if (!is_string($name) || preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) !== 1) {
            throw new RuntimeException("Invalid template {$kind} name.");
        }
    }
}
