<?php

namespace RomanStruk\SmsNotify\Tests;

use Mockery;
use Mockery\MockInterface;
use RomanStruk\SmsNotify\Clients\MtsCommunicator\MtsCommunicator;
use RomanStruk\SmsNotify\Clients\MtsCommunicator\MtsCommunicatorClient;
use RomanStruk\SmsNotify\Clients\MtsCommunicator\ResponseMessage;
use RomanStruk\SmsNotify\Message\SmsMessage;
use RomanStruk\SmsNotify\PhoneNumber\PhoneNumber;
use RomanStruk\SmsNotify\Response;

class MtsCommunicatorTest extends TestCase
{
    public function test_client_can_parse_valid_response()
    {
        /* @var $clientMock MtsCommunicatorClient */
        $clientMock = Mockery::mock(MtsCommunicatorClient::class , function (MockInterface $mock){
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn(
                    new \GuzzleHttp\Psr7\Response(200, [], $this->getResponseJson('mts_send_success'), JSON_THROW_ON_ERROR)
                );
        })->makePartial();

        $response = $clientMock->singleMessages('0666000000', 'Some message', 'sms');

        $this->assertEquals('9f60ac8f-e721-5027-b838-e6fcb95fcd7a', $response->current()->getId());
    }

    public function test_client_can_parse_failed_response()
    {
        /* @var $clientMock MtsCommunicatorClient */
        $clientMock = Mockery::mock(MtsCommunicatorClient::class , function (MockInterface $mock){
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn(
                    new \GuzzleHttp\Psr7\Response(200, [], $this->getResponseJson('mts_send_failed'), JSON_THROW_ON_ERROR)
                );
        })->makePartial();

        $response = $clientMock->singleMessages('0666000000', 'Some message', 'sms');

        $this->assertEquals('Phone number incorrect', $response->current()->getErrorMessage());
    }


    public function test_client_can_send_message()
    {
        /* @var $client MtsCommunicator*/
        $client = Mockery::mock(MtsCommunicator::class, function (MockInterface $mock){
            $mock
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn(
                    new Response($this->getResponseJson('mts_send_success'), ResponseMessage::class)
                );
        })->makePartial();

        $response = $client->to(new PhoneNumber('0661234567', 'UA'))->send(new SmsMessage('test message'));

        $this->assertEquals('9f60ac8f-e721-5027-b838-e6fcb95fcd7a', $response->current()->getId());
    }
}