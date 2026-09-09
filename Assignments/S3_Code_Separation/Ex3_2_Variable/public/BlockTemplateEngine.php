<?php

namespace WPROG2\S3_Code_Separation\Ex3_2_Variable\public;

class BlockTemplateEngine
{
    private array $parts;

    public function __construct(string $filePath, string $separator = "<!--===xxx===-->")
    {
        $html = file_get_contents($filePath);
        if ($html === false) {
            throw new \RuntimeException("Error: Could not read file path '$filePath'");
        }

        $this->parts = explode($separator, $html);
    }

    public function render(array $data): string
    {
        $renderedRows = '';
        $rowTemplate = $this->parts[1];

        foreach ($data as $name => $value) {
            $displayValue = is_array($value) ? implode(', ', $value) : (string)$value;

            $row = str_replace('---name---', htmlspecialchars((string)$name), $rowTemplate);
            $row = str_replace('---value---', htmlspecialchars($displayValue), $row);

            $renderedRows .= $row;
        }

        return $this->parts[0] . $renderedRows . $this->parts[2];
    }
}