<?php

declare(strict_types=1);

namespace TaylorR\TelegramAPI;

use pocketmine\plugin\Plugin;
use TaylorR\TelegramAPI\client\Client;
use TaylorR\TelegramAPI\events\EditedTextEvent;
use TaylorR\TelegramAPI\events\ReplyMessageEvent;
use TaylorR\TelegramAPI\events\SendTextEvent;
use TaylorR\TelegramAPI\user\User;

class TelegramBot extends Client
{

    public function __construct(
        protected string $token,
        protected Plugin $plugin,
        array $options = []
    ){
        parent::__construct($token, $plugin, $options);
        $this->checkToken();
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function checkToken(): void
    {
        $response = $this->request('getMe');
        if ($response === false) {
            throw new \Exception($response['description']);
        }
    }

    public function processUpdate(array $update): void
    {
        if ($this->options['debug']) {
            $this->plugin->getLogger()->debug('Update: ' . json_encode($update));
        }

        $message = $update['message'] ?? null;
        $editedMessage = $update['edited_message'] ?? null;

        if ($message) {
            $text = $message['text'] ?? null;
            $from = $message['from'] ?? null;
        
            if ($text) {
                $user = new User($from['username'], $from['first_name'], $from['is_bot'], $from['id']);
                $ev = new SendTextEvent($user, $text);
                $ev->call();
        
                foreach ($this->textRegexCallback as $regex => $callback) {
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

                $callback = $this->replyListeners[$chatId . $messageId] ?? null;
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
        
                foreach ($this->editedListeners as $callback) {
                    $callback($editedMessage);
                }
            }
        }
    }

    /**
     * @param string $method
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function onText(string $regex, callable $callback): void
    {
        $this->textRegexCallback[$regex] = $callback;
    }

    /**
     * @param int $chatId
     * @param int $messageId
     * @param callable $callback
     */
    public function onReplyToMessage(int $chatId, int $messageId, callable $callback): void
    {
        $this->replyListeners[$chatId . $messageId] = $callback;
    }

    /**
     * @param callable $callback
     */
    public function onEditedText(callable $callback): void
    {
        $this->editedListeners[] = $callback;
    }
}