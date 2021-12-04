<?php

namespace RomanStruk\SmsNotify;

use Illuminate\Support\Facades\Facade;
use RomanStruk\SmsNotify\Contracts\SmsNotifyInterface;

class SmsNotifyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SmsNotifyInterface::class;
    }

}