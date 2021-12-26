<?php

namespace RomanStruk\SmsNotify\Contracts;

use Closure;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;

interface SmsNotifyInterface
{
    public function to(PhoneNumberInterface $phoneNumber): SmsNotifyInterface;

    public function send(MessageInterface $message): ResponseInterface;

    public function client(string $alias, array $configuration = []): SmsNotifyInterface;

    public function clientMap(Closure $closure): SmsNotifyInterface;
}