<?php

namespace RomanStruk\SmsNotify\Contracts;

interface ClientChannelInterface
{
    public function setChannel(string $channel);
}