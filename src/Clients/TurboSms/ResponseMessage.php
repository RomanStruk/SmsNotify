<?php

namespace RomanStruk\SmsNotify\Clients\TurboSms;

use RomanStruk\SmsNotify\Response\Message;

class ResponseMessage extends Message
{
    protected $messageIdKey = 'message_id';

    protected $statusKey = 'response_code';

    protected $errorKey = 'response_status';
}