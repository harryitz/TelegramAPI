<?php

declare(strict_types=1);

namespace TaylorR\TelegramAPI\events;

use pocketmine\event\Event;
use TaylorR\TelegramLog\user\User;

class ReplyMessageEvent extends Event
{

    public function __construct(
        private User $user,
        private User $replyuser,
        private string $replyText,
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
     * @return User
     */
    public function getReplyUser(): User
    {
        return $this->replyuser;
    }

    /**
     * @return string
     */
    public function getReplyText(): string
    {
        return $this->replyText;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}