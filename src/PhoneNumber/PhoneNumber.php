<?php

namespace RomanStruk\SmsNotify\PhoneNumber;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;

class PhoneNumber implements PhoneNumberInterface
{
    /**
     * @var array
     */
    private $numbers;

    /**
     * @var PhoneNumberUtil
     */
    private $phoneUtil;

    /**
     * @param string|array $numbers
     * @param string $numberRegion
     */
    public function __construct($numbers, string $numberRegion)
    {

        $this->phoneUtil = PhoneNumberUtil::getInstance();

        if (!is_array($numbers)){
            $numbers = [$numbers];
        }
        foreach ($numbers as $number) {
            try {
                $this->numbers[] = $this->phoneUtil->parse($number, $numberRegion);
            } catch (NumberParseException $e) {
                //TODO $e
            }
        }
    }

    public function isValidNumbers(): bool
    {
        foreach ($this->numbers as $number) {
            if(! $this->phoneUtil->isValidNumber($number)){
                return false;
            }
        }
        return true;
    }

    public function implode(string $separator = ', '): string
    {
        return implode($separator, $this->toArray());
    }

    public function toArray(): array
    {
        $formatted = [];
        foreach ($this->numbers as $number) {
            $formatted[] = $this->phoneUtil->format($number, PhoneNumberFormat::E164);
        }
        return $formatted;
    }

    public function first(): string
    {
        return $this->toArray()[0];
    }
}