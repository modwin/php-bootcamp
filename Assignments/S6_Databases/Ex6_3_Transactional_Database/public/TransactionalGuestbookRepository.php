<?php

declare(strict_types=1);
namespace WPROG2\S6_Databases\Ex6_3_Transactional_Database\public;
use PDO;

final readonly class TransactionalGuestbookRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function addEntryWithImage(GuestbookEntry $entry, ?array $imageData = null): bool
    {
        $this->pdo->beginTransaction();

        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO entries (name, email, website, comment)
                 VALUES (:name, :email, :website, :comment)'
            );

            $statement->execute([
                ':name' => $entry->getName(),
                ':email' => $entry->getEmail(),
                ':website' => $entry->getWebsite(),
                ':comment' => $entry->getComment(),
            ]);

            if ($imageData !== null) {
                $entryId = (int) $this->pdo->lastInsertId();

                $imageStatement = $this->pdo->prepare(
                    'INSERT INTO images (entry_id, mime_type, image_data)
                     VALUES (:entry_id, :mime_type, :image_data)'
                );

                $imageStatement->execute([
                    ':entry_id' => $entryId,
                    ':mime_type' => $imageData['mime_type'],
                    ':image_data' => $imageData['data'],
                ]);
            }

            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();

            throw $e;
        }
    }

    public function beginTransaction(GuestbookEntry $entry): bool
    {
        return true;
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
                (string)$row['name'],
                (string)$row['email'],
                (string)$row['website'],
                (string)$row['comment'],
                (string)$row['created_at'],
                (int)$row['id'],
            );
        }

        return $entries;
    }

    private function commit(): void
    {

    }

    private function rollback(): void
    {

    }


}
