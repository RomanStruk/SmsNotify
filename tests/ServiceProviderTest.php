<?php
namespace RomanStruk\SmsNotify\Tests;

use Mockery;
use Mockery\Mock;
use Mockery\MockInterface;
use RomanStruk\SmsNotify\Clients\Log;
use RomanStruk\SmsNotify\Clients\ViberUa\Viber;
use RomanStruk\SmsNotify\Clients\ViberUa\ViberClient;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Contracts\SmsNotifyInterface;
use RomanStruk\SmsNotify\Message\SmsMessage;
use RomanStruk\SmsNotify\PhoneNumber\PhoneNumber;
use RomanStruk\SmsNotify\Response\Response;
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
        $response = SmsNotifyFacade::clientMap(function ($smsnotify){
            return 'ua';
        })
            ->to(new PhoneNumber('0666000000'))
            ->send(new SmsMessage('Some text'));

        $this->assertInstanceOf(Log::class, $response->getSenderClient());
    }

    public function test_basic_use()
    {
        $response = SmsNotifyFacade::client('log')
            ->to(new PhoneNumber('0666000000'))
            ->send(new SmsMessage('Some text'));

        $this->assertInstanceOf(ResponseInterface::class, $response);

    }

    public function test_debug_mode_is_on()
    {
        $smsNotify = SmsNotifyFacade::client('log');
        $smsNotify->enableDebug();
        $response = $smsNotify
            ->to(new PhoneNumber('0666000000'))
            ->send(new SmsMessage('Some text'));
        $debug = $response->getDebugInformation();

        $this->assertEquals('0666000000', $debug['numbers']);
        $this->assertEquals('Some text', $debug['message']);
        $this->assertEquals(Log::class, $debug['client']);
    }
}