<?php

namespace RomanStruk\SmsNotify\Response;

use RomanStruk\SmsNotify\Contracts\Response\DeliveryReportInterface;
use RomanStruk\SmsNotify\Contracts\Response\SuccessDeliveryReportInterface;

class SuccessDeliveryReport implements SuccessDeliveryReportInterface, DeliveryReportInterface
{

    private $phoneNumber;
    private $messageId;
    private $status;
    private $code;

    public function __construct($phoneNumber, $messageId, $status, $code)
    {
        $this->phoneNumber = $phoneNumber;
        $this->messageId = $messageId;
        $this->status = $status;
        $this->code = $code;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }
}