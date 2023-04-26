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
            // TODO: Implement onText() method.
        }

        if ($editedMessage){
            // TODO: Implement onEditedText() method.
        }

        if ($channelPost){
            // TODO: Implement onChannelPost() method.
        }
    }
}