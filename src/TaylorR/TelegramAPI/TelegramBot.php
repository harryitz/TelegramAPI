<?php

namespace TaylorR\TelegramAPI;

use TaylorR\TelegramAPI\client\Client;

class TelegramBot extends Client
{

    public function __construct(
        protected string $token,
        array $options = []
    ){
        parent::__construct($token, $options);
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
        // Process an update; emitting the proper events and executing regexp
        // callbacks. This method is useful should you be using a different
        // way to fetch updates, other than those provided by TelegramBot.
    }
}