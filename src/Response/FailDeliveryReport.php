<?php

namespace RomanStruk\SmsNotify\Response;

use RomanStruk\SmsNotify\Contracts\Response\DeliveryReportInterface;

class FailDeliveryReport implements DeliveryReportInterface
{
    private $phoneNumber;
    private $status;
    private $code;

    public function __construct(string $phoneNumber, string $status, int $code)
    {
        $this->phoneNumber = $phoneNumber;
        $this->status = $status;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}