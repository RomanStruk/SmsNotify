<?php

namespace RomanStruk\SmsNotify\Clients\ViberUa;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use RomanStruk\SmsNotify\Contracts\ClientChannelInterface;
use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Response\FailDeliveryReport;
use RomanStruk\SmsNotify\Response\Response;

class Viber implements ClientInterface, ClientChannelInterface
{
    const CHANNEL_VIBER_SMS = 'viber_sms';
    const CHANNEL_VIBER = 'viber';
    const CHANNEL_SMS = 'sms';

    /**
     * @var ViberClient
     */
    private $client;

    /**
     * @var string
     */
    private $message_id;

    /**
     * Канали відправки
     * @var string
     */
    private $channel = 'viber';

    /**
     * @var string
     */
    private $numbers;

    /**
     * @var ResponseInterface|Response
     */
    private $response;

    /**
     * @var bool
     */
    private $debug;

    public function __construct($config)
    {
        if (!$config['token']) {
            throw new InvalidArgumentException('Need token');
        }
        if (!$config['sender_vb'] || !$config['sender_sms']) {
            throw new InvalidArgumentException('Need alfa name for viber or sms');
        }

        $this->client = new ViberClient(
            $config['token'],
            $config['sender_vb'],
            $config['sender_sms']
        );
        $this->setChannel($config['default_channel']);
        $this->setOption('name', $config['sender_vb']);
    }

    /**
     * @param MessageInterface $message
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function send(MessageInterface $message): ResponseInterface
    {
        try {
            $response = $this->request($this->numbers, $message->getMessage());
        }catch (ClientException $e) {
            $guzzleResponse = $e->getResponse();
            if ($guzzleResponse->getStatusCode() === 401){
                $fail = new FailDeliveryReport($this->numbers, 'Unauthenticated', 401);

            }else{
                $fail = new FailDeliveryReport($this->numbers, $e->getMessage(), $guzzleResponse->getStatusCode());
            }
            $response = new Response($fail);
        }

        $response->setSenderClient($this);

        return $response;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    protected function request(string $phone, string $message)
    {
        return call_user_func([$this->client, "{$this->channel}Request"], $phone, $message);
    }


    /**
     * Інформація про повідомлення
     * @param null $id
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function statusMessage($id = null): ResponseInterface
    {
        if (is_null($id) && !is_null($this->message_id)) {
            $message_id = $this->message_id;
        } else {
            $message_id = $id;
        }
        try {
            $this->response = $this->client->statusRequest($message_id);
        } catch (RequestException $e) {
            $this->response = new Response(500, false);
            $this->response->setErrors([$e->getMessage()]);
        } catch (\Exception $exception) {
            $this->response = new Response(500, false);
            $this->response->setErrors([$exception->getMessage()]);
        }
        $this->response->setSenderClient($this);
        return $this->response;
    }

    /**
     * @param string $channel
     */
    public function setChannel(string $channel)
    {
        $this->channel = $channel;
    }

    public function setOption($option, $value): void
    {
        if ($option === 'name') {
            $this->client->setMailingName($value);
        }
    }

    public function to(PhoneNumberInterface $phoneNumber): ClientInterface
    {
        $this->numbers = $phoneNumber->getNumber();
        return $this;
    }

    public function debug(bool $mode): ClientInterface
    {
        $this->debug = $mode;
        if ($mode){
            $this->client = new FakeViberClient();
        }
        return $this;
    }
}