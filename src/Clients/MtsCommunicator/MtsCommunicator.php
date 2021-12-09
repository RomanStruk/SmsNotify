<?php

namespace RomanStruk\SmsNotify\Clients\MtsCommunicator;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\ResponseInterface;

class MtsCommunicator implements ClientInterface
{

    /**
     * @var MtsCommunicatorClient
     */
    private $client;

    /**
     * List of Message delivery channels.â–ª Any combination without duplicates, e.g. Viber + SMS or Push + Viber + SMS
     * @var string
     */
    private $channel;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var PhoneNumberInterface
     */
    private $phoneNumber;

    /**
     * @var MessageInterface
     */
    private $message;

    public function __construct($config)
    {
        if (!$config['login']) {
            throw new InvalidArgumentException('Need login');
        }
        if (!$config['password']) {
            throw new InvalidArgumentException('Need password');
        }
        if (!$config['client_id']) {
            throw new InvalidArgumentException('Need client_id');
        }
        if (!$config['alfa_name']) {
            throw new InvalidArgumentException('Need alfa name');
        }

        $this->client = new MtsCommunicatorClient($config['login'], $config['password'], $config['client_id'], $config['alfa_name']);

        $this->setChannel($config['default_channel']);

        $this->channel = 'sms';

    }

    /**
     * @param string $channel
     * @return void
     */
    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * Send Message
     * @param MessageInterface $message
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function send(MessageInterface $message): ResponseInterface
    {
        $this->message = $message;
        $this->response = $this->client->request($this->phoneNumber->getNumber(), $this->message->getMessage(), $this->channel);
        $this->response->setSenderClient($this);
        return $this->response;
    }

    public function setOption($option, $value): void
    {
    }

    public function to(PhoneNumberInterface $phoneNumber): ClientInterface
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function debug(bool $mode): ClientInterface
    {
        return $this;
    }
}
