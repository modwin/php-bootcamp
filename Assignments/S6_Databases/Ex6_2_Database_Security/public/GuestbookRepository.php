<?php

namespace WPROG2\S6_Databases\Ex6_2_Database_Security\public;

class GuestbookRepository
{
    public function saveEntry($name, $email, $website, $comment): bool
    {
        return true;
    }

    public function findAll(): array
    {
        return [];
    }


}