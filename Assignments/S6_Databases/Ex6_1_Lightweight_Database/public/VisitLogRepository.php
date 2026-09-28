<?php

namespace WPROG2\S6_Databases\Ex6_1_Lightweight_Database\public;


class VisitLogRepository
{
    public function __construct(
        private \PDO $pdo
    ) {}

    public function addVisit(VisitLog $visit): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO visits (visited_at, remote_address, user_agent)
             VALUES (:visited_at, :remote_address, :user_agent)'
        );

        return $stmt->execute([
            ':visited_at' => $visit->getCurrentTime(),
            ':remote_address' => $visit->getRemoteAddress(),
            ':user_agent' => $visit->getHttpUserAgent(),
        ]);
    }

    /**
     * @return VisitLog[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT visited_at, remote_address, user_agent
         FROM visits
         ORDER BY id ASC'
        );

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $visits = [];

        foreach ($rows as $row) {
            $visits[] = new VisitLog(
                $row['visited_at'],
                $row['remote_address'],
                $row['user_agent']
            );
        }

        return $visits;
    }

}