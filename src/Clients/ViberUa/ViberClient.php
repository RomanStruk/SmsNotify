<?php

namespace RomanStruk\SmsNotify\Clients\ViberUa;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\ResponseInterface as GuzzleResponseInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Response\FailDeliveryReport;
use RomanStruk\SmsNotify\Response\Response;
use RomanStruk\SmsNotify\Response\SuccessDeliveryReport;

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
            return $this->parseResponse($guzzleResponse, $recipients);
        } catch (ClientException $e) {
            $guzzleResponse = $e->getResponse();
            if ($guzzleResponse->getStatusCode() === 401) {
                $fail = new FailDeliveryReport($recipients, 'Unauthenticated', 401);

            } else {
                $fail = new FailDeliveryReport($recipients, $e->getMessage(), $guzzleResponse->getStatusCode());
            }
            return new Response($fail);
        }
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
     * @throws JsonException
     */
    public function statusRequest($id_message): ResponseInterface
    {
        $guzzleResponse = $this->request($this->api_urls['status'], $this->headers, ['id' => $id_message]);
        if ($guzzleResponse->getStatusCode() === 200) {
            $content = json_decode($guzzleResponse->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            // відповідь для статусу повідомлення
            if (array_key_exists('query_status', $content) && $content['query_status'] === 'success') {
                $success = new SuccessDeliveryReport($content['id'], $content['id'], implode(', ', [$content['query_status'], $content['status_name']]), 200);
                return new Response($success);
            }
        }
        return new Response(new FailDeliveryReport($id_message, 'Something wrong', 500));
    }

    /**
     * @throws JsonException
     */
    private function parseResponse(GuzzleResponseInterface $guzzleResponse, $recipients = null): ResponseInterface
    {
        if ($guzzleResponse->getStatusCode() === 200) {
            $content = json_decode($guzzleResponse->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            //відповідь для відправки повідомлень
            if (array_key_exists('status', $content) && $content['status'] === 'success') {
                $success = new SuccessDeliveryReport($recipients, $content['id'], implode(', ', $content), 200);
                return new Response($success);
            }
        }
        return new Response(new FailDeliveryReport($recipients, 'Something wrong', 500));
    }

    /**
     * @param string $name
     */
    public function setMailingName(string $name): void
    {
        $this->name = $name;
    }

}