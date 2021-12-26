<?php
namespace RomanStruk\SmsNotify\Tests;

use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Contracts\SmsNotifyInterface;
use RomanStruk\SmsNotify\Message\SmsMessage;
use RomanStruk\SmsNotify\PhoneNumber\PhoneNumber;
use RomanStruk\SmsNotify\SmsNotifyFacade;

class ServiceProviderTest extends TestCase
{

    public function test_service_provider_bind_class()
    {
        $smsNotify = app(SmsNotifyInterface::class);
        $this->assertInstanceOf(SmsNotifyInterface::class, $smsNotify);
    }

    public function test_client_map_use()
    {
        $response = SmsNotifyFacade::clientMap(function (){
            return 'ua';
        })
            ->to(new PhoneNumber('0666000000', 'UA'))
            ->send(new SmsMessage('Some text'));

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function test_debug_mode_is_on()
    {
        $smsNotify = SmsNotifyFacade::client('log');
        $smsNotify->enableDebug();
        $response = $smsNotify
            ->to(new PhoneNumber('0666000000', 'UA'))
            ->send(new SmsMessage('Some text'));
        $debug = $response->current()->toArray();

        $this->assertEquals(['+380666000000'], $debug['numbers']);
        $this->assertEquals('Some text', $debug['message_text']);
    }
}