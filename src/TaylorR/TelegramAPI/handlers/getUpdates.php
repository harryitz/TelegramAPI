<?php

declare(strict_types=1);

namespace TaylorR\TelegramAPI\handlers;

use pocketmine\scheduler\Task;
use pocketmine\utils\Internet;
use TaylorR\TelegramAPI\client\Client;

class getUpdates extends Task
{

    protected function __construct(
        private Client $client,
        private string $url
    ){}

    public function onRun(): void
    {
        $url = $this->url . 'getUpdates';
        $result = Internet::postURL($url, json_encode([
            'offset' => $this->client->lastUpdateId + 1,
            'timeout' => 10
        ]), 10, [
            'Content-Type: application/json'
        ]);
        $response = json_decode($result->getBody(), true);
        if ($response['ok'] === false) {
            throw new \Exception($response['description']);
        }
        foreach ($response['result'] as $update) {
            $this->client->processUpdate($update);
            $this->client->lastUpdateId = $update['update_id'];
        }
    }
}