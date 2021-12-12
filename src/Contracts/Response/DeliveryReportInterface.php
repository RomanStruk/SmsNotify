<?php

namespace RomanStruk\SmsNotify\Contracts\Response;

interface DeliveryReportInterface
{
    public function getStatus(): string;

    public function getCode(): int;

    public function getPhoneNumber(): string;
}