<?php

namespace RomanStruk\SmsNotify\Clients\TurboSms;

use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Exceptions\InvalidClientConfigurationException;

class TurboSms implements ClientInterface
{
    /**
     * @var TurboSmsClient
     */
    private $client;

    /**
     * @var PhoneNumberInterface
     */
    private $phoneNumber;

    /**
     * @throws InvalidClientConfigurationException
     */
    public function __construct($config)
    {
        if (!$config['token']) {
            throw new InvalidClientConfigurationException('Need token');
        }
        if (!$config['sender'] || !$config['sender_sms']) {
            throw new InvalidClientConfigurationException('Need alfa name for viber or sms');
        }

        $this->client = new TurboSmsClient($config['token'], $config['sender_sms']);
    }

    public function to(PhoneNumberInterface $phoneNumber): ClientInterface
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function send(MessageInterface $message): ResponseInterface
    {
        return $this->request($this->phoneNumber->toArray(), $message->getMessage());
    }

    protected function request($numbers, $message): ResponseInterface
    {
        return $this->client->sendSms($numbers, $message);
    }
}