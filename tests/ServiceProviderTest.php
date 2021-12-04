<?php
namespace RomanStruk\SmsNotify\Tests;

use RomanStruk\SmsNotify\Contracts\SmsNotifyInterface;

class ServiceProviderTest extends TestCase
{

    public function test_service_provider_bind_class()
    {
        $smsNotify = app(SmsNotifyInterface::class);
        $this->assertInstanceOf(SmsNotifyInterface::class, $smsNotify);
    }
}