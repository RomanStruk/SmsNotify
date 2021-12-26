<?php

namespace RomanStruk\SmsNotify\Clients\TurboSms;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface as GuzzleResponseInterface;
use RomanStruk\SmsNotify\Response;

class TurboSmsClient
{
    protected $urlMessageSend = 'https://api.turbosms.ua/message/send.json';
    protected $urlMessageSendPing = 'https://api.turbosms.ua/message/ping.json';
    protected $urlUserBalance = 'https://api.turbosms.ua/user/balance.json';

    /**
     * @var string[]
     */

    private $headers;

    /**
     * @var GuzzleHttpClient
     */
    private $guzzleClient;

    private $sender;

    public function __construct(string $token, string $sender)
    {
        $this->sender = $sender;

        $this->guzzleClient = new GuzzleHttpClient();

        $this->headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept-Charset' => 'utf-8',
        ];
    }

    /**
     * @param array $recipients
     * @param string $text
     * @return array
     */
    protected function prepareSmsJson(array $recipients, string $text): array
    {
        return [
            'recipients' => $recipients,
            'sms' => [
                'sender' => $this->sender,
                'text' => $text
            ]
        ];
    }

    public function sendSms(array $recipients, string $text): Response
    {
        try {
            $guzzleResponse = $this->request($this->urlMessageSend, $this->headers, $this->prepareSmsJson($recipients, $text));
            $json = $guzzleResponse->getBody()->getContents();
        } catch (ClientException $exception) {
            $json = $exception->getResponse()->getBody()->getContents();
        }
        return new Response($json, ResponseMessage::class, 'response_result');
    }

    /**
     * @throws GuzzleException
     */
    public function sendSmsPing(array $recipients, string $text): Response
    {
        $json = $this->request(
            $this->urlMessageSendPing,
            $this->headers,
            $this->prepareSmsJson($recipients, $text)
        )
            ->getBody()
            ->getContents();

        return new Response($json, ResponseMessage::class, 'response_result');
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
}