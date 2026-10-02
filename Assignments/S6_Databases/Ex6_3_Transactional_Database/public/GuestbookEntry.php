<?php

declare(strict_types=1);

namespace WPROG2\S6_Databases\Ex6_3_Transactional_Database\public;

final readonly class GuestbookEntry
{
    public function __construct(
        private string $name,
        private string $email,
        private string $website,
        private string $comment,
        private ?string $createdAt = null,
        private ?int $id = null,
        private ?string $image = null,
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            "NAMN: %s\nEMAIL: %s\nHEMSIDA: %s\nKOMMENTAR: %s\nSKAPAT: %s\nBILD: %s",
            $this->name,
            $this->email,
            $this->website,
            $this->comment,
            $this->createdAt ?? '',
            $this->image ?? '',
        );
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

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
}
