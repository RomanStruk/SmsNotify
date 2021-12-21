<?php

namespace RomanStruk\SmsNotify\Clients\TurboSms;

use InvalidArgumentException;
use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;

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
     * @var MessageInterface
     */
    private $message;
    /**
     * @var bool
     */
    private $debug;

    public function __construct($config)
    {
        if (!$config['token']) {
            throw new InvalidArgumentException('Need token');
        }
        if (!$config['sender'] || !$config['sender_sms']) {
            throw new InvalidArgumentException('Need alfa name for viber or sms');
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
        $this->message = $message;
        $response = $this->client->sendSms($this->phoneNumber->toArray(), $message->getMessage());

        $response->setSenderClient($this);
        return $response;
    }

    public function debug(bool $mode): ClientInterface
    {
        $this->debug = $mode;
        return $this;
    }
}