<?php

namespace RomanStruk\SmsNotify\Contracts;

interface PhoneNumberInterface
{
    public function isValidNumbers(): bool;

    public function toArray(): array;

    public function implode(string $separator = ', '): string;

    public function first(): string;
}