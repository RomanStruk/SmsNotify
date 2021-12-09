<?php

namespace RomanStruk\SmsNotify\Contracts;

interface ClientInterface
{
    public function to(PhoneNumberInterface $phoneNumber): ClientInterface;

    public function send(MessageInterface $message): ResponseInterface;

    public function debug(bool $mode): ClientInterface;
}