<?php

namespace Adiafora\Api\Bitrix24;

use Adiafora\Bitrix24\Exceptions\ApiBitrix24Exception;

class Bitrix24
{
    /**
     * @var string OAuth-токен вебхука
     */
    private $token;

    /**
     * @var int
     */
    private $fromUserId;

    /**
     * @var string Домен компании в Битрикс24
     */
    private $domain;

    /**
     * Bitrix24 constructor.
     *
     * @throws ApiBitrix24Exception
     */
    public function __construct()
    {
        try {
            $this->token = config('bitrix24_notice.token');

        } catch (\Exception $e) {

            throw new ApiBitrix24Exception('Not found token in the config/bitrix24_notice');
        }

        try {
            $this->fromUserId = (int)config('bitrix24_notice.fromUserId');

        } catch (\Exception $e) {

            throw new ApiBitrix24Exception('Not found fromUserId in the config/bitrix24_notice');
        }

        try {
            $this->domain = config('bitrix24_notice.domain');

        } catch (\Exception $e) {

            throw new ApiBitrix24Exception('Not found domain in the config/bitrix24_notice');
        }
    }

    /**
     * Подготовка и выполнение запроса к API Битрикс24
     *
     * @param $params array
     * @return mixed
     * @throws ApiBitrix24Exception
     */
    public function send(array $params)
    {
        $headers = [
            "Accept-Language: ru",
            "Content-Type: application/json; charset=utf-8"
        ];

        $body = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $streamOptions = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => $headers,
                'content' => $body
            ],
        ]);

        try {
            $result = file_get_contents($this->geuUrlForSend(), 0, $streamOptions);

            return $result;

        } catch (\Exception $e) {

            throw new ApiBitrix24Exception($e->getMessage());
        }
    }

    /**
     * Адрес сервиса для отправки JSON-запросов (регистрозависимый)
     *
     * @return string
     *
     */
    private function geuUrlForSend()
    {
        return 'https://' . $this->domain . '.bitrix24.ru/rest/' . $this->fromUserId . '/' . $this->token . '/im.message.add.json';
    }
}
