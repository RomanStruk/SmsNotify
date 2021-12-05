<?php

namespace RomanStruk\SmsNotify\Clients\ViberUa;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use InvalidArgumentException;
use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\ResponseInterface;
use RomanStruk\SmsNotify\Response\Response;

class Viber implements ClientInterface
{
    const CHANNEL_VIBER_SMS = 'viber_sms';
    const CHANNEL_VIBER = 'viber';
    const CHANNEL_SMS = 'sms';

    /**
     * @var ViberClient
     */
    public $client;

    /**
     * @var string
     */
    private $message_id;

    /**
     * Канали відправки
     * @var string
     */
    private $channel = 'viber';

    private string $numbers;
    /**
     * @var ResponseInterface|Response
     */
    private $response;

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

    public function send(MessageInterface $message): ResponseInterface
    {
        try {
            $this->response = $this->request($this->numbers, $message->getMessage());
            $this->message_id = $this->response->getMessageId();
        }catch (ClientException $e) {
            $this->response = new Response(500, false);
            $this->response->setErrors([$e->getMessage()]);
        }
        return $this->response;
    }

    protected function request(string $phone, string $message)
    {
        $response = new Response();
        switch ($this->channel){
            case self::CHANNEL_VIBER_SMS:
            case self::CHANNEL_SMS:
            case self::CHANNEL_VIBER:
                return $this->client->viberRequest($phone, $message);
            default:
        }
        return $response;
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
            $this->response = $this->client->requestStatus($message_id);
        } catch (RequestException $e) {
            $this->response = new Response(500, false);
            $this->response->setErrors([$e->getMessage()]);
        } catch (\Exception $exception) {
            $this->response = new Response(500, false);
            $this->response->setErrors([$exception->getMessage()]);
        }
        return $this->response;
    }

    /**
     * @param string $channel
     */
    public function setChannel(string $channel): void
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
}