<?php

namespace RomanStruk\SmsNotify\Contracts\Response;

interface SuccessDeliveryReportInterface
{
    public function getMessageId(): string;
}