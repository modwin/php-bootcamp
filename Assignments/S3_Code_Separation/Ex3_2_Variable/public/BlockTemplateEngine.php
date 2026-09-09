<?php

namespace WPROG2\S3_Code_Separation\Ex3_2_Variable\public;

class BlockTemplateGetter
{
    private string $html;
    private array $contents;
    private readonly string $separator;

    public function __construct($file_name, $separator)
    {
        $this->$separator = $separator;
        $this->html = file_get_contents($file_name);
    }

    public function getContents(): array
    {
        return $this->contents;
    }

    public function setContents(array $contents): void
    {
        $this->contents = explode("$this->separator", $this->html);
    }
}