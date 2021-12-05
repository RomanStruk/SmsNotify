<?php

namespace RomanStruk\SmsNotify\Contracts;

interface PhoneNumberInterface
{
    public function getNumber($implodeSeparator = ', '): string;
}