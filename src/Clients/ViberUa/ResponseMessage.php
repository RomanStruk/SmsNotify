<?php

namespace RomanStruk\SmsNotify\Clients\ViberUa;

use RomanStruk\SmsNotify\Response\Message;

class ResponseMessage extends Message
{
    protected $messageIdKey = 'id';

    protected $errorKey = 'message';

    public function getErrorMessage(): string
    {
        if (!isset($this->data[$this->statusKey]) || $this->data[$this->statusKey] !== 'error') {
            return '';
        }

        return $this->checkData($this->errorKey);
    }
}