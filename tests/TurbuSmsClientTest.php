<?php
namespace RomanStruk\SmsNotify\Tests;

use RomanStruk\SmsNotify\Clients\TurboSms\TurboSmsClient;

class TurbuSmsClientTest extends TestCase
{
    public function test_sms_send_ping()
    {
        $client = new TurboSmsClient('59a575cb3a2bebf2befbfac492c2d4d64decb30c', 'Test');
        $response = $client->sendSmsPing(['0666666666'], 'test text');

        $this->assertEquals('PONG', $response->getStatus('0666666666'));
    }

    public function test_send_sms()
    {
        $client = new TurboSmsClient('59a575cb3a2bebf2befbfac492c2d4d64decb30c', 'MAGAZIN');
        $response = $client->sendSms(['380668514453'], 'test text');
dd($response);
    }
}