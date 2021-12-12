<?php

namespace RomanStruk\SmsNotify\Clients;

use Illuminate\Support\Facades\Log as LaravelLog;
use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Response\Response;
use RomanStruk\SmsNotify\Response\SuccessDeliveryReport;

class Log implements ClientInterface
{
    /**
     * @var PhoneNumberInterface $phoneNumber
     */
    private $phoneNumber;

    /**
     * @var MessageInterface $message
     */
    private $message;

    public function to(PhoneNumberInterface $phoneNumber): ClientInterface
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function send(MessageInterface $message): ResponseInterface
    {
        $this->message = $message;

        LaravelLog::channel('syslog')->info('SmsNotify', [$this->formatMessage()]);

        $response = new Response(new SuccessDeliveryReport($this->phoneNumber->getNumber(), '', $this->formatMessage(), 200));
        $response->setSenderClient($this);
        $response->setDebugInformation('numbers', $this->phoneNumber->getNumber());
        $response->setDebugInformation('message', $message->getMessage());
        $response->setDebugInformation('client', self::class);
        return $response;
    }

    protected function formatMessage(): string
    {
        return $this->phoneNumber->getNumber() .' ' . $this->message->getMessage();
    }

    public function debug(bool $mode): ClientInterface
    {
        return $this;
    }
}