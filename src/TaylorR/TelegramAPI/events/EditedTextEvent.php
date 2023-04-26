<?php

declare(strict_types=1);

namespace TaylorR\TelegramAPI\events;

use pocketmine\event\Event;
use TaylorR\TelegramAPI\user\User;

class EditedTextEvent extends Event
{

    public function __construct(
        private User $user,
        private string $text
    ){}

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}