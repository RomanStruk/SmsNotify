<?php

namespace RomanStruk\SmsNotify\Tests;


use RomanStruk\SmsNotify\PhoneNumber\PhoneNumber;

class PhoneNumberTest extends TestCase
{
    public function test_phone_number_class_can_parse_a_number()
    {
        $phoneNumber = new PhoneNumber('06685144444', 'UA');

        $this->assertEquals(['+3806685144444'], $phoneNumber->toArray());

        $this->assertEquals('+3806685144444', $phoneNumber->implode());

        $this->assertEquals('+3806685144444', $phoneNumber->first());

    }

    public function test_phone_number_class_can_parse_numbers()
    {
        $phoneNumber = new PhoneNumber(['06685144444', '+38(099)85-14-4444'], 'UA');

        $this->assertEquals(['+3806685144444', '+3809985144444'], $phoneNumber->toArray());

        $this->assertEquals('+3806685144444, +3809985144444', $phoneNumber->implode(', '));

        $this->assertEquals('+3806685144444', $phoneNumber->first());

    }
}
