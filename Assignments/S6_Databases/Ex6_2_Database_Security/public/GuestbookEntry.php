<?php

namespace WPROG2\S6_Databases\Ex6_2_Database_Security\public;

class GuestbookEntry
{


    /**
     * @param string $name
     * @param string $email
     * @param string $website
     * @param string $comment
     * @param string $createdAt
     */
    public function __construct(private readonly string $name,
                                private readonly string $email,
                                private readonly string $website,
                                private readonly string $comment,
                                private readonly string $createdAt)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}