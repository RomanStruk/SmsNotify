<?php

namespace RomanStruk\SmsNotify\PhoneNumber;

use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;

class PhoneNumber implements PhoneNumberInterface
{
    /**
     * @var array
     */
    private $numbers;

    /**
     * @param string|array $numbers
     */
    public function __construct($numbers)
    {
        if (!is_array($numbers)){
            $numbers = [$numbers];
        }
        $this->validate($numbers);
        $this->numbers = $numbers;
    }

    protected function validate($numbers): void
    {

    }

    public function getNumber($implodeSeparator = ', '): string
    {
        return implode($implodeSeparator, $this->numbers);
    }

    public function getNumbers()
    {
        return $this->numbers;
    }
}