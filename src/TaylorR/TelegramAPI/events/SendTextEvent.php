<?php

declare(strict_types=1);

namespace TaylorR\TelegramAPI\events;

use pocketmine\event\Event;

class SendTextEvent extends Event
{

    public function __construct(
        private string $text
    ){}

    public function getText(): string
    {
        return $this->text;
    }
}