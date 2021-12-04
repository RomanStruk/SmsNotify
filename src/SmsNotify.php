<?php

namespace RomanStruk\SmsNotify;

use RomanStruk\SmsNotify\Contracts\SmsNotifyInterface;

class SmsNotify implements SmsNotifyInterface
{
    public function __construct($config)
    {

    }

    public function client(string $alias)
    {

        return config('smsnotify.default');
    }
}