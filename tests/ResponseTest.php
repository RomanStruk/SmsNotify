<?php

namespace RomanStruk\SmsNotify\Tests;


use RomanStruk\SmsNotify\Clients\TurboSms\ResponseMessage;
use RomanStruk\SmsNotify\Response;

class ResponseTest extends TestCase
{
    public function test_response_parse_json_for_turbosms()
    {
        $data = [
            "response_code" => 802,
            "response_status" => "SUCCESS_MESSAGE_PARTIAL_ACCEPTED",
            "response_result" => [
                [
                    "phone" => "отримувач_1",
                    "response_code" => 406,
                    "message_id" => null,
                    "response_status" => "NOT_ALLOWED_RECIPIENT_COUNTRY"
                ],
                [
                    "phone" => "отримувач_2",
                    "response_code" => 0,
                    "message_id" => "f83f8868-5e46-c6cf-e4fb-615e5a293754",
                    "response_status" => "OK"
                ]
            ]
        ];
        $response = new Response(json_encode($data), ResponseMessage::class, 'response_result');

        self::assertEquals(406, $response->current()->getStatus());
        self::assertEquals(null, $response->current()->getId());

        $response->next();

        self::assertEquals(0, $response->current()->getStatus());
        self::assertEquals('f83f8868-5e46-c6cf-e4fb-615e5a293754', $response->current()->getId());
    }

    public function test_response_parse_json_fail_request_turbosms()
    {
        $content = '{"response_code":103,"response_status":"REQUIRED_TOKEN","response_result":null}';
        $response = new Response($content, ResponseMessage::class, 'response_result');

        dd($response->current()->getErrorMessage());
    }
}