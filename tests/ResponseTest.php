<?php

namespace RomanStruk\SmsNotify\Tests;


use RomanStruk\SmsNotify\Clients\TurboSms\ResponseMessage;
use RomanStruk\SmsNotify\Response;
use function Webmozart\Assert\Tests\StaticAnalysis\null;

class ResponseTest extends TestCase
{
    public function test_response_parse_json_for_turbosms()
    {
        $json = '{"response_code":802,"response_status":"SUCCESS_MESSAGE_PARTIAL_ACCEPTED","response_result":[{"phone":"\u043e\u0442\u0440\u0438\u043c\u0443\u0432\u0430\u0447_1","response_code":406,"message_id":null,"response_status":"NOT_ALLOWED_RECIPIENT_COUNTRY"},{"phone":"\u043e\u0442\u0440\u0438\u043c\u0443\u0432\u0430\u0447_2","response_code":0,"message_id":"f83f8868-5e46-c6cf-e4fb-615e5a293754","response_status":"OK"}]}';
        $response = new Response($json, ResponseMessage::class, 'response_result');

        self::assertEquals(406, $response->current()->getStatus());
        self::assertEquals(null, $response->current()->getId());

        $response->next();

        self::assertEquals(0, $response->current()->getStatus());
        self::assertEquals('f83f8868-5e46-c6cf-e4fb-615e5a293754', $response->current()->getId());
    }

    public function test_response_parse_json_fail_request_turbosms()
    {
        $json = '{"response_code":103,"response_status":"REQUIRED_TOKEN","response_result":null}';
        $response = new Response($json, ResponseMessage::class, 'response_result');

        self::assertEquals('REQUIRED_TOKEN', $response->current()->getErrorMessage());
    }

    public function test_response_parse_json_for_viber_success()
    {
        $json = '{"status": "success","code": "s00","id": "f83f8868-5e46-c6cf-e4fb-615e5a293754","message": "SMS dispatch was created"}';
        $response = new Response($json, \RomanStruk\SmsNotify\Clients\ViberUa\ResponseMessage::class, null);

        self::assertEquals('f83f8868-5e46-c6cf-e4fb-615e5a293754', $response->current()->getId());
    }
}