<?php

namespace RomanStruk\SmsNotify\Clients\ViberUa;

use Illuminate\Support\Str;
use RomanStruk\SmsNotify\Contracts\ResponseInterface;
use RomanStruk\SmsNotify\Response\Response;

class FakeViberClient
{
    /**
     */
    public function viberRequest($recipients, $message): ResponseInterface
    {
        $response = new Response(200, true);
        $response->setMessageId(Str::random());
        $response->setMessage($message);
        return $response;
    }
}