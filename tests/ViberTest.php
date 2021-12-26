<?php

namespace RomanStruk\SmsNotify\Tests;

use Mockery;
use Mockery\MockInterface;
use RomanStruk\SmsNotify\Clients\ViberUa\ResponseMessage;
use RomanStruk\SmsNotify\Clients\ViberUa\Viber;
use RomanStruk\SmsNotify\Clients\ViberUa\ViberClient;
use RomanStruk\SmsNotify\Message\SmsMessage;
use RomanStruk\SmsNotify\PhoneNumber\PhoneNumber;
use RomanStruk\SmsNotify\Response;
use RomanStruk\SmsNotify\SmsNotifyFacade;

class ViberTest extends TestCase
{
    public function test_client_can_send_message()
    {
        $res = new Response(
            $this->getResponseJson('viber_ua_send_success'),
            ResponseMessage::class);

        $viberClient = Mockery::mock(Viber::class, function (MockInterface $mock) use ($res) {
            $mock
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn($res);
        })->makePartial();


        $response = $viberClient->to(new PhoneNumber('0661234567', 'UA'))->send(new SmsMessage('test message'));

        $this->assertEquals('f83f8868-5e46-c6cf-e4fb-615e5a293754', $response->current()->getId());
    }

    public function test_viber_request_client_return_valid_response()
    {
        $viberClientMock = Mockery::mock(ViberClient::class , function (MockInterface $mock){
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn(
                    new \GuzzleHttp\Psr7\Response(200, [], $this->getResponseJson('viber_ua_send_success'))
                );
        })->makePartial();

        $response = $viberClientMock->viberRequest('0666000000', 'Some message');

        $this->assertEquals('f83f8868-5e46-c6cf-e4fb-615e5a293754', $response->current()->getId());
    }

    public function test_viber_client_can_parse_failed_response()
    {
        $viberClientMock = Mockery::mock(ViberClient::class , function (MockInterface $mock){
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn(
                    new \GuzzleHttp\Psr7\Response(200, [], $this->getResponseJson('viber_ua_send_failed'))
                );
        })->makePartial();

        $response = $viberClientMock->viberRequest('0666000000', 'Some message');

        $this->assertEquals('Something wrong', $response->current()->getErrorMessage());
    }
}
