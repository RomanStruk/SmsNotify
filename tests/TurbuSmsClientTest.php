<?php
namespace RomanStruk\SmsNotify\Tests;

use Mockery;
use Mockery\MockInterface;
use RomanStruk\SmsNotify\Clients\TurboSms\ResponseMessage;
use RomanStruk\SmsNotify\Clients\TurboSms\TurboSms;
use RomanStruk\SmsNotify\Clients\TurboSms\TurboSmsClient;
use RomanStruk\SmsNotify\Message\SmsMessage;
use RomanStruk\SmsNotify\PhoneNumber\PhoneNumber;
use RomanStruk\SmsNotify\Response;

class TurbuSmsClientTest extends TestCase
{
    public function test_turbosms_client_can_parse_valid_response()
    {
        /* @var $clientMock TurboSmsClient */
        $clientMock = Mockery::mock(TurboSmsClient::class , function (MockInterface $mock){
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn(
                    new \GuzzleHttp\Psr7\Response(200, [], $this->getResponseJson('turbosms_send_success'))
                );
        })->makePartial();

        $response = $clientMock->sendSms(['0666000000'], 'Some message');

        $this->assertEquals('f83f8868-5e46-c6cf-e4fb-615e5a293754', $response->current()->getId());
    }

    public function test_turbosms_can_parse_valid_response()
    {
        /* @var $client TurboSms*/
        $client = Mockery::mock(TurboSms::class, function (MockInterface $mock){
            $mock
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn(
                    new Response(
                        $this->getResponseJson('turbosms_send_success'),
                        ResponseMessage::class,
                        'response_result')
                );
        })->makePartial();

        $response = $client->to(new PhoneNumber('0661234567', 'UA'))->send(new SmsMessage('test message'));

        $this->assertEquals('f83f8868-5e46-c6cf-e4fb-615e5a293754', $response->current()->getId());
    }
}