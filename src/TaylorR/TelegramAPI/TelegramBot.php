<?php

declare(strict_types=1);

namespace TaylorR\TelegramAPI;

use pocketmine\plugin\Plugin;
use TaylorR\TelegramAPI\client\Client;

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
        if ($response['ok'] === false) {
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
        $channelPost = $update['channel_post'] ?? null;


        if ($message){
            $text = $message['text'] ?? null;
            if ($text){
                foreach ($this->textRegexCallback as $regex => $callback){
                    if (preg_match($regex, $text, $matches)){
                        $callback($matches, $message);
                    }
                }
            }
            $replyToMessage = $message['reply_to_message'] ?? null;
            if ($replyToMessage){
                $chatId = $replyToMessage['chat']['id'];
                $messageId = $replyToMessage['message_id'];
                $callback = $this->replyListeners[$chatId . $messageId] ?? null;
                if ($callback){
                    $callback($message);
                }
            }
        }

        if ($editedMessage){
            // TODO: Implement onEditedText() method.
        }

        if ($channelPost){
            // TODO: Implement onChannelPost() method.
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
}