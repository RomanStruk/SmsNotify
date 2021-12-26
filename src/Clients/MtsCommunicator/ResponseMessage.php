<?php

namespace RomanStruk\SmsNotify\Clients\MtsCommunicator;

use RomanStruk\SmsNotify\Response\Message;
use RuntimeException;

class ResponseMessage extends Message
{
    protected $messageIdKey = 'message_id';

    protected $errorKey = 'error_text';

    protected $errorStatusKey = 'error_code';

    public function getStatus()
    {
        try {
            $this->checkData($this->messageIdKey);
            return '1';
        }catch (RuntimeException $exception){
            return $this->checkData($this->errorStatusKey);
        }
    }
}