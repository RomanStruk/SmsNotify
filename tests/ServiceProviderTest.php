<?php
namespace RomanStruk\SmsNotify\Tests;

use Mockery;
use Mockery\Mock;
use Mockery\MockInterface;
use RomanStruk\SmsNotify\Clients\ViberUa\Viber;
use RomanStruk\SmsNotify\Clients\ViberUa\ViberClient;
use RomanStruk\SmsNotify\Contracts\ResponseInterface;
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
            ->to(new PhoneNumber('0668514453'))
            ->send(new SmsMessage('Some text'));

        $this->assertInstanceOf(ResponseInterface::class, $response);

    }
    public function test_basic_use()
    {
        $response = SmsNotifyFacade::client('log')
            ->to(new PhoneNumber('0668514453'))
            ->send(new SmsMessage('Some text'));

        $this->assertInstanceOf(ResponseInterface::class, $response);

    }

    public function test_viber_request_client()
    {
        $viberClientMock = Mockery::mock(ViberClient::class , function (MockInterface $mock){
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn(
                    new \GuzzleHttp\Psr7\Response(200, [], json_encode(['status' => 'success', 'id'=>99]))
                );
        })->makePartial();

        $response = $viberClientMock->viberRequest('0998514444', 'Some message');

        $this->assertEquals(99, $response->messageId);

    }

    public function test_viber_client()
    {
        $res = new Response(200, true, 'success');
        $res->setMessageId(99);

        $viberClient = Mockery::mock(Viber::class, function (MockInterface $mock) use ($res) {
            $mock
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn($res);
        })->makePartial();


        $response = $viberClient->to(new PhoneNumber('0661234567'))->send(new SmsMessage('test message'));

        $this->assertEquals(99, $response->getMessageId());
    }
}