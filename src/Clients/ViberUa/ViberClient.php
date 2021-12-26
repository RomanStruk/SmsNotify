<?php

namespace RomanStruk\SmsNotify\Clients\ViberUa;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface as GuzzleResponseInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Response;

class ViberClient
{
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
     * @var string
     */
    private $token;

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

    public function viberRequest(string $recipients, string $message): ResponseInterface
    {
        try {
            $guzzleResponse = $this->request($this->api_urls['viber'], $this->headers, $this->prepareViberRequest($recipients, $message));
            $json = $guzzleResponse->getBody()->getContents();
        } catch (ClientException $e) {
            $json = $e->getResponse()->getBody()->getContents();
        }
        return new Response($json, ResponseMessage::class);
    }

    /**
     * @param $url
     * @param $headers
     * @param $json
     * @return GuzzleResponseInterface
     * @throws GuzzleException
     */
    protected function request($url, $headers, $json): GuzzleResponseInterface
    {
        return $this->guzzleClient->post($url, ['headers' => $headers, 'json' => $json]);
    }

    /**
     * Статус відправленого повідомлення по id
     * @param $id_message
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function statusRequest($id_message): ResponseInterface
    {
        $guzzleResponse = $this->request($this->api_urls['status'], $this->headers, ['id' => $id_message]);

        return new Response($guzzleResponse->getBody()->getContents(), ResponseMessage::class);
    }

    /**
     * @param string $name
     */
    public function setMailingName(string $name): void
    {
        $this->name = $name;
    }

}