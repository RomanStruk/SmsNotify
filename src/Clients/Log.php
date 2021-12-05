<?php

namespace RomanStruk\SmsNotify\Clients;

use Illuminate\Support\Facades\Log as LaravelLog;
use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\ResponseInterface;
use RomanStruk\SmsNotify\Response\Response;

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

        return new Response();
    }

    protected function formatMessage(): string
    {
        return $this->phoneNumber->getNumber() .' ' . $this->message->getMessage();
    }
}