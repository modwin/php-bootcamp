<?php

namespace WPROG2\S6_Databases\Ex6_2_Database_Security\public;

use PDO;

class GuestbookRepository
{

    public function __construct(
        private PDO $pdo
    )
    {
    }

    public function addEntry(GuestbookEntry $entry): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO entries (name, email, website, comment, created_at) 
                    VALUES (:name, :email, :website, :comment, :created_at)'
        );

        return $stmt->execute([':name' => $entry->getName(),
            ':email' => $entry->getEmail(),
            ':website' => $entry->getWebsite(),
            ':created_at' => $entry->getCreatedAt()]);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT name, email, website, comment, created_at
            FROM entries,
                 ORDER BY created_at DESC');

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $entries = [];

        foreach ($rows as $row) {
            $entries[] = new GuestbookEntry(
                $row['name'],
                $row['email'],
                $row['website'],
                $row['comment'],
                $row['createdAt']
            );
        }
        return $entries;
    }


}