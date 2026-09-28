<?php

namespace WPROG2\S6_Databases\Ex6_2_Database_Security\public;

class GuestbookRepository
{
    private readonly \PDO $pdo;

    public function __construct($pdo)
    {
        $this->$pdo = $pdo;
    }

    public function addEntry(GuestbookEntry $entry): bool
    {
        return true;
    }

    public function findAll(): array
    {

        return [];
    }


}