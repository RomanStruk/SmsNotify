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
        $client = new ViberClient('sdfsdf', 'sdfsdf', 'sdfsdf');
//        $client = $this->getMockBuilder(ViberClient::class)->disableOriginalConstructor()->getMock();
        // спробувати розщирити клас і переоприділити guzzle
        $viberClientMock = Mockery::mock($client, function (MockInterface $mock){
            $mock->shouldReceive('guzzleClient->post')
                ->andReturn(
                    new \GuzzleHttp\Psr7\Response(200, [], json_encode(['status' => 'success', 'id'=>99]))
                );
        });
dd($viberClientMock);
        $viberClientMock->viberRequest('0998514444', 'Some message');

    }

    public function test_viber_client()
    {
        $res = new Response(200, true, 'success');
        $res->setMessageId(99);
        $viberClientMock = Mockery::mock(ViberClient::class, function (MockInterface $mock) use ($res) {
            $mock->shouldReceive('viberRequest')
//                ->andReturn(
//                    new \GuzzleHttp\Psr7\Response(200, [], json_encode(['status' => 'success', 'id'=>99]))
//                );
                ->andReturn($res);
        });

        $viberClient = new Viber([
            'token' => 'sdfsadfs345345',
            'sender_vb' => 'sdfsdf',
            'sender_sms' => 'sdfsdf',
            'default_channel' => 'viber',
        ]);
        $viberClient->client = $viberClientMock;

        $response = $viberClient->to(new PhoneNumber('s4554sdf'))->send(new SmsMessage('test'));

        $this->assertEquals(99, $response->getMessageId());
    }
}