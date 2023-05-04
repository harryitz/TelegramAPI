<?php

declare(strict_types=1);

namespace TaylorR\TelegramAPI;

use pocketmine\plugin\Plugin;
use pocketmine\Server;
use TaylorR\TelegramAPI\client\Client;
use TaylorR\TelegramChat\handlers\AsyncUpdate;

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
        Server::getInstance()->getAsyncPool()->submitTask(new AsyncUpdate($this, $update));
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