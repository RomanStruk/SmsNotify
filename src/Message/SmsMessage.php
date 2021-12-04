<?php

namespace RomanStruk\SmsNotify\Message;

use RomanStruk\SmsNotify\Contracts\MessageInterface;

class SmsMessage implements MessageInterface
{
    /**
     * @var string
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getMessage(): string
    {
        return $this->text;
    }
}