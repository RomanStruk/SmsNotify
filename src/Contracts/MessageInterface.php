<?php

namespace RomanStruk\SmsNotify\Contracts;

interface MessageInterface
{
    public function getMessage(): string;
}