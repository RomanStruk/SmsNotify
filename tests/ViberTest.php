<?php

namespace RomanStruk\SmsNotify\Tests;

use Mockery;
use Mockery\MockInterface;
use RomanStruk\SmsNotify\Clients\ViberUa\Viber;
use RomanStruk\SmsNotify\Clients\ViberUa\ViberClient;
use RomanStruk\SmsNotify\Message\SmsMessage;
use RomanStruk\SmsNotify\PhoneNumber\PhoneNumber;
use RomanStruk\SmsNotify\Response\Response;
use RomanStruk\SmsNotify\Response\SuccessDeliveryReport;
use RomanStruk\SmsNotify\SmsNotifyFacade;

class ViberTest extends TestCase
{
    public function test_viber_client_is_send_message()
    {
        $res = new Response(new SuccessDeliveryReport('0661234567', 99, 'OK', 200));

        $viberClient = Mockery::mock(Viber::class, function (MockInterface $mock) use ($res) {
            $mock
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn($res);
        })->makePartial();


        $response = $viberClient->to(new PhoneNumber('0661234567'))->send(new SmsMessage('test message'));

        $this->assertEquals(99, $response->getMessageId());
    }

    public function test_viber_request_client_return_valid_response()
    {
        $viberClientMock = Mockery::mock(ViberClient::class , function (MockInterface $mock){
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('request')
                ->andReturn(
                    new \GuzzleHttp\Psr7\Response(200, [], json_encode(['status' => 'success', 'id' => 99], JSON_THROW_ON_ERROR))
                );
        })->makePartial();

        $response = $viberClientMock->viberRequest('0666000000', 'Some message');

        $this->assertEquals(99, $response->getMessageId('0666000000'));

    }

    public function test_viber_auth_fail_response()
    {
        $smsNotify = SmsNotifyFacade::client('viber', ['token' => 'fake token', 'sender_vb' => 'fake', 'sender_sms' => 'fake', 'default_channel' => 'viber']);
        $response = $smsNotify
            ->to(new PhoneNumber('0666000000'))
            ->send(new SmsMessage('Some text'));

        $this->assertEquals('Unauthenticated', $response->getStatus('0666000000'));
    }
}
