<?php

namespace WPROG2\S3_Code_Separation\Ex3_1_Static\public;

class Counter
{
    private string $file_name;

    public function __construct(string $file_name)
    {
        $this->file_name = $file_name;
    }

    public function incrementAndGet(): int
    {
        $existing_file = fopen($this->file_name, "c+");
        if (!$existing_file) {
            throw new \RuntimeException("Error: Unable to open counter file.");
        }

        if (!flock($existing_file, LOCK_EX)) {
            fclose($existing_file);
            throw new \RuntimeException("Error: Unable to lock the counter file.");
        }

        $raw_file_content = fread($existing_file, 64);

        $count = abs((int) trim($raw_file_content)) + 1;

        ftruncate($existing_file, 0);
        rewind($existing_file);

        fwrite($existing_file, (string) $count);
        fflush($existing_file);

        flock($existing_file, LOCK_UN);

        fclose($existing_file);

        return $count;
    }

}