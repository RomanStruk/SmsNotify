<?php

namespace RomanStruk\SmsNotify\Clients\MtsCommunicator;

use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Exceptions\InvalidClientConfigurationException;
use RomanStruk\SmsNotify\Exceptions\UnauthorizedException;
use RomanStruk\SmsNotify\Response;

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

    /**
     * @throws InvalidClientConfigurationException
     */
    public function __construct($config)
    {
        if (!$config['login']) {
            throw new InvalidClientConfigurationException('Need login');
        }
        if (!$config['password']) {
            throw new InvalidClientConfigurationException('Need password');
        }
        if (!$config['client_id']) {
            throw new InvalidClientConfigurationException('Need client_id');
        }
        if (!$config['alfa_name']) {
            throw new InvalidClientConfigurationException('Need alfa name');
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
     * @throws UnauthorizedException
     */
    public function send(MessageInterface $message): ResponseInterface
    {
        $this->message = $message;
        $this->response = $this->request($this->phoneNumber->implode(), $this->message->getMessage());
        return $this->response;
    }

    /**
     * @throws UnauthorizedException
     */
    protected function request(string $phone, string $message): Response
    {
        return $this->client->singleMessages($phone, $message, $this->channel);
    }

    public function setOption($option, $value): void
    {
    }

    public function to(PhoneNumberInterface $phoneNumber): ClientInterface
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
}
