<?php

namespace RomanStruk\SmsNotify\Tests;

use Illuminate\Support\Str;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Response\Response;
use RomanStruk\SmsNotify\Response\SuccessDeliveryReport;

class FakeViberClient
{
    /**
     */
    public function viberRequest($recipients, $message): ResponseInterface
    {
        return new Response(new SuccessDeliveryReport('0666666666', Str::random(), $message, 20));
    }
}