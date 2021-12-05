<?php

namespace RomanStruk\SmsNotify;

use Illuminate\Support\Facades\Facade;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\ResponseInterface;
use RomanStruk\SmsNotify\Contracts\SmsNotifyInterface;

/**
 * @method static ResponseInterface send(MessageInterface $message)
 * @method static SmsNotifyInterface to(PhoneNumberInterface $phoneNumber)
 * @method static SmsNotifyInterface clientMap(\Closure $closure)
 *
 * @see SmsNotify
*/
class SmsNotifyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SmsNotifyInterface::class;
    }

}