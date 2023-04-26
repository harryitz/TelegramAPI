<?php

declare(strict_types=1);

namespace TaylorR\TelegramAPI\user;

class User
{

    public function __construct(
        private string $username,
        private string $first_name,
        private bool $is_bot,
        private int $id
    ){}

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function isBot(): bool
    {
        return $this->is_bot;
    }

    public function getId(): int
    {
        return $this->id;
    }
}