<?php

declare(strict_types=1);

namespace TaylorR\TelegramChat\handlers;

use pocketmine\scheduler\AsyncTask;
use TaylorR\TelegramAPI\client\Client;
use TaylorR\TelegramAPI\events\EditedTextEvent;
use TaylorR\TelegramAPI\events\ReplyMessageEvent;
use TaylorR\TelegramAPI\events\SendTextEvent;
use TaylorR\TelegramAPI\user\User;

class AsyncUpdate extends AsyncTask
{

    public function __construct(
        protected Client $client,
        protected array $update
    ){}

    public function onRun(): void
    {
        $client = $this->client;
        $update = $this->update;
        $message = $update['message'] ?? null;
        $editedMessage = $update['edited_message'] ?? null;

        if ($message) {
            $text = $message['text'] ?? null;
            $from = $message['from'] ?? null;
        
            if ($text) {
                $user = new User($from['username'], $from['first_name'], $from['is_bot'], $from['id']);
                $ev = new SendTextEvent($user, $text);
                $ev->call();
        
                foreach ($client->textRegexCallback as $regex => $callback) {
                    if (preg_match($regex, $text, $matches)) {
                        $callback($matches, $message);
                    }
                }
            }
            $replyToMessage = $message['reply_to_message'] ?? null;
            if ($replyToMessage){
                $chatId = $replyToMessage['chat']['id'];
                $messageId = $replyToMessage['message_id'];
                $replyFrom = $replyToMessage['from'] ?? null;
                if ($replyFrom) {
                    $replyUser = new User($replyFrom['username'], $replyFrom['first_name'], $replyFrom['is_bot'], $replyFrom['id']);
                    $ev = new ReplyMessageEvent($user, $replyUser, $replyToMessage['text'], $message['text']);
                    $ev->call();
                }

                $callback = $client->replyListeners[$chatId . $messageId] ?? null;
                if ($callback) {
                    $callback($message);
                }
            }
        }

        if ($editedMessage){
            $from = $editedMessage['from'] ?? null;
            $text = $editedMessage['text'] ?? null;
            if ($from && $text) {
                $user = new User($from['username'], $from['first_name'], $from['is_bot'], $from['id']);
                $ev = new EditedTextEvent($user, $text);
                $ev->call();
        
                foreach ($client->editedListeners as $callback) {
                    $callback($editedMessage);
                }
            }
        }
    }
}