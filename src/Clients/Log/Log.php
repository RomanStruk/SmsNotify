<?php

namespace RomanStruk\SmsNotify\Clients\Log;

use Illuminate\Support\Facades\Log as LaravelLog;
use Illuminate\Support\Str;
use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Response;

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

        $json = json_encode([
            'messages' => [
                [
                    'status' => 'OK',
                    'message_id' => Str::random(),
                    'message_text' => $message->getMessage(),
                    'numbers' => $this->phoneNumber->toArray()
                ]
            ]
        ], JSON_THROW_ON_ERROR);
        return new Response($json, ResponseMessage::class, 'messages');
    }

    protected function formatMessage(): string
    {
        return $this->phoneNumber->first() .' ' . $this->message->getMessage();
    }

    public function debug(bool $mode): ClientInterface
    {
        return $this;
    }
}