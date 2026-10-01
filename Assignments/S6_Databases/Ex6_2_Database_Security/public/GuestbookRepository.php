<?php

declare(strict_types=1);

namespace WPROG2\S6_Databases\Ex6_2_Database_Security\public;

use PDO;

final readonly class GuestbookRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function addEntry(GuestbookEntry $entry): bool
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO entries (name, email, website, comment)
             VALUES (:name, :email, :website, :comment)',
        );

        return $statement->execute([
            ':name' => $entry->getName(),
            ':email' => $entry->getEmail(),
            ':website' => $entry->getWebsite(),
            ':comment' => $entry->getComment(),
        ]);
    }

    /** @return list<GuestbookEntry> */
    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, name, email, website, comment, created_at
             FROM entries
             ORDER BY created_at DESC, id DESC',
        );

        $entries = [];
        foreach ($statement->fetchAll() as $row) {
            $entries[] = new GuestbookEntry(
                (string) $row['name'],
                (string) $row['email'],
                (string) $row['website'],
                (string) $row['comment'],
                (string) $row['created_at'],
                (int) $row['id'],
            );
        }

        return $entries;
    }
}
