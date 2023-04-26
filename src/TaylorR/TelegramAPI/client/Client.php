<?php

declare(strict_types=1);

namespace TaylorR\TelegramAPI\client;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\Internet;
use TaylorR\TelegramAPI\handlers\getUpdates;

abstract class Client
{

    protected array $options = [];

    private TaskScheduler $scheduler;

    public int $lastUpdateId;

    public array $textRegexCallback, $replyListeners;

    public function __construct(
        protected string $token,
        protected PluginBase $plugin,
        array $options = []
    ){
        $this->options['baseApiUrl'] = $options['baseApiUrl'] ?? 'https://api.telegram.org/bot';
        $this->options['badRejection'] = $options['badRejection'] ?? false;
        $this->options['debug'] = $options['debug'] ?? false;
        $this->options['timeUpdate'] = $options['timeUpdate'] ?? 20;
        $this->textRegexCallback = [];
        $this->replyListeners = [];
        $this->scheduler = $this->plugin->getScheduler();

        $this->scheduler->scheduleRepeatingTask(new getUpdates(
            $this,
            $this->getApiUrl()
        ), $this->options['timeUpdate']);
    }
    
    abstract public function processUpdate(array $update): void;

    /**
     * @return string
     */
    protected function getApiUrl(): string
    {
        return $this->options['baseApiUrl'] . $this->token . '/';
    }

    /**
     * @param string $method
     * @param array $params
     * @return array
     * @throws \Exception
     */
    protected function request(string $method, array $params = []): array
    {
        $url = $this->getApiUrl() . $method;
        $result = Internet::postURL($url, json_encode($params), 10, [
            'Content-Type: application/json'
        ]);
        $response = json_decode($result->getBody(), true);
        if ($response['ok'] === false && $this->options['badRejection'] === true) {
            throw new \Exception($response['description']);
        }
        return $response['result'] ?? [];
    }

    /**
     * @param int $fileId
     * @param array $params
     * @return array
     */
    public function getFile(
        int $fileId,
        array $params = []
    ): array{
        $params['file_id'] = $fileId;
        return $this->request('getFile', $params);
    }

    /**
     * @param int $fileId
     * @param array $params
     * @return string
     */
    public function getFileLink(
        int $fileId,
        array $params = []
    ): string{
        $file = $this->getFile($fileId, $params);
        return $this->options['baseApiUrl'] . '/file/bot' . $this->token . '/' . $file['file_path'];
    }

    /**
     * @param array $params
     * @return array
     */
    public function getMe(
        array $params = []
    ): array{
        return $this->request('getMe', $params);
    }

    /**
     * @param array $params
     * @return array
     */

    public function logOut(
        array $params = []
    ): array{
        return $this->request('logOut', $params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function close(
        array $params = []
    ): array{
        return $this->request('close', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function sendMessage(
        int|string $chatId,
        string $text,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['text'] = $text;
        return $this->request('sendMessage', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function forwardMessage(
        int|string $chatId,
        int|string $fromChatId,
        int $messageId,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['from_chat_id'] = $fromChatId;
        $params['message_id'] = $messageId;
        return $this->request('forwardMessage', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function copyMessage(
        int|string $chatId,
        int|string $fromChatId,
        int $messageId,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['from_chat_id'] = $fromChatId;
        $params['message_id'] = $messageId;
        return $this->request('copyMessage', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function sendPhoto(
        int|string $chatId,
        string $photo,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['photo'] = $photo;
        return $this->request('sendPhoto', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function sendAudio(
        int|string $chatId,
        string $audio,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['audio'] = $audio;
        return $this->request('sendAudio', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function sendDocument(
        int|string $chatId,
        string $document,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['document'] = $document;
        return $this->request('sendDocument', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function sendVideo(
        int|string $chatId,
        string $video,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['video'] = $video;
        return $this->request('sendVideo', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function sendAnimation(
        int|string $chatId,
        string $animation,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['animation'] = $animation;
        return $this->request('sendAnimation', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function sendVoice(
        int|string $chatId,
        string $voice,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['voice'] = $voice;
        return $this->request('sendVoice', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function sendVideoNote(
        int|string $chatId,
        string $videoNote,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['video_note'] = $videoNote;
        return $this->request('sendVideoNote', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function sendMediaGroup(
        int|string $chatId,
        array $media,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['media'] = $media;
        return $this->request('sendMediaGroup', $params);
    }

    /**
     * @param int|string $chatId
     * @param array $params
     * @return array
     */
    public function sendLocation(
        int|string $chatId,
        float $latitude,
        float $longitude,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['latitude'] = $latitude;
        $params['longitude'] = $longitude;
        return $this->request('sendLocation', $params);
    }

    /**
     * @param int|string $chatId
     * @param string $venue
     * @param array $params
     * @return array
     */
    public function editMessageLiveLocation(
        float $latitude,
        float $longitude,
        array $params = []
    ): array{
        $params['latitude'] = $latitude;
        $params['longitude'] = $longitude;
        return $this->request('editMessageLiveLocation', $params);
    }

    /**
     * @param int|string $chatId
     * @param string $venue
     * @param array $params
     * @return array
     */
    public function stopMessageLiveLocation(
        array $params = []
    ): array{
        return $this->request('stopMessageLiveLocation', $params);
    }

    /**
     * @param int|string $chatId
     * @param string $venue
     * @param array $params
     * @return array
     */
    public function sendVenue(
        int|string $chatId,
        float $latitude,
        float $longitude,
        string $title,
        string $address,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['latitude'] = $latitude;
        $params['longitude'] = $longitude;
        $params['title'] = $title;
        $params['address'] = $address;
        return $this->request('sendVenue', $params);
    }

    /**
     * @param int|string $chatId
     * @param string $venue
     * @param array $params
     * @return array
     */
    public function sendContact(
        int|string $chatId,
        string $phoneNumber,
        string $firstName,
        array $params = []
    ): array{
        $params['chat_id'] = $chatId;
        $params['phone_number'] = $phoneNumber;
        $params['first_name'] = $firstName;
        return $this->request('sendContact', $params);
    }
}