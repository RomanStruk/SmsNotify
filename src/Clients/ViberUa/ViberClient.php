<?php

namespace RomanStruk\SmsNotify\Clients\ViberUa;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\ResponseInterface as GuzzleResponseInterface;
use RomanStruk\SmsNotify\Contracts\ResponseInterface;
use RomanStruk\SmsNotify\Response\Response;

class ViberClient
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $api_urls = [
        'user-info' => 'https://my2.viber.net.ua/api/v2/user/info',
        'sms' => 'https://my2.viber.net.ua/api/v2/sms/dispatch',
        'viber' => 'https://my2.viber.net.ua/api/v2/viber/dispatch',
        'status' => 'https://my2.viber.net.ua/api/v2/viber/status'
    ];

    /**
     * @var GuzzleHttpClient
     */
    protected $guzzleClient;
    /**
     * @var string[]
     */
    private $headers = [];

    /**
     * @var string
     */
    private $sender_vb;

    /**
     * @var string
     */
    private $sender_sms;

    /**
     * @var string
     */
    private $name = 'Mailing name';

    /**
     * @param string $token
     * @param string $sender_vb
     * @param string $sender_sms
     */
    public function __construct(string $token, string $sender_vb, string $sender_sms)
    {
        $this->token = $token;
        $this->sender_vb = $sender_vb;
        $this->sender_sms = $sender_sms;
        $this->initHttpClient();
    }

    private function initHttpClient(): void
    {
        $this->guzzleClient = new GuzzleHttpClient();
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Accept-Charset' => 'utf-8',
        ];
    }

    private function prepareSmsRequest($recipients, $message): array
    {
        return [
            'name' => $this->name,        //Название рассылки, от 4 до 60 символов.
            'recipients' => $recipients,        //Номер (или номера) получателя сообщения, в международном формате: код страны и код сети плюс номер телефона.
            'sender' => $this->sender_sms,      //Адрес отправителя, не более 11 латинских символов.Адрес должен быть зарегистрирован в "Личном кабинете" в разделе "Viber" - "Список отправителей".
            'message' => $message,           //Текст сообщения, не более 1000 символов
        ];
    }

    private function prepareViberRequest($recipients, $message): array
    {
        return [
            'name' => $this->name,        //Название рассылки, от 4 до 60 символов.
            'recipients' => $recipients,        //Номер (или номера) получателя сообщения, в международном формате: код страны и код сети плюс номер телефона.
            'sender' => $this->sender_vb,      //Адрес отправителя, не более 11 латинских символов.Адрес должен быть зарегистрирован в "Личном кабинете" в разделе "Viber" - "Список отправителей".
            'message' => $message,           //Текст сообщения, не более 1000 символов
        ];
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function smsRequest($recipients, $message): ResponseInterface
    {
        $guzzleResponse =  $this->guzzleClient->post($this->api_urls['sms'], [
            'headers' => $this->headers,
            'json' => $this->prepareSmsRequest($recipients, $message)
        ]);
        return $this->parseResponse($guzzleResponse);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function viberRequest($recipients, $message): ResponseInterface
    {
        $guzzleResponse = $this->guzzleClient->post($this->api_urls['viber'], [
            'headers' => $this->headers,
            'json' => $this->prepareViberRequest($recipients, $message)
        ]);
        return $this->parseResponse($guzzleResponse);
    }

    /**
     * Статус відправленого повідомлення по id
     * @param $id_message
     * @return ResponseInterface
     * @throws GuzzleException
     * @throws JsonException
     */
    public function requestStatus($id_message): ResponseInterface
    {
        return $this->parseResponse($this->guzzleClient->post($this->api_urls['status'], [
            'headers' => $this->headers,
            'json' => [
                'id' => $id_message
            ]
        ]));
    }

    /**
     * @throws JsonException
     */
    private function parseResponse(GuzzleResponseInterface $guzzleResponse): ResponseInterface
    {
        $response = new Response(500, false);
        if ($guzzleResponse->getStatusCode() === 200) {
            $response->setSuccess(true);
            $response->setStatusCode($guzzleResponse->getStatusCode());

            $content = json_decode($guzzleResponse->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            //відповідь для відправки повідомлень
            if (array_key_exists('status', $content) && $content['status'] === 'success') {
                $response->setMessage(implode(', ', $content));
                $response->setMessageId(implode($content['id']));

                return $response;
            }
            // відповідь для статусу повідомлення
            if (array_key_exists('query_status', $content) && $content['query_status'] === 'success') {
                $response->setMessage(implode(', ', [$content['query_status'], $content['status_name']]));

                return $response;
            }

            $response->setErrors($content);
        }
        return $response;
    }

    /**
     * @param string $name
     */
    public function setMailingName(string $name): void
    {
        $this->name = $name;
    }

}