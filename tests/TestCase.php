<?php
namespace RomanStruk\SmsNotify\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use RomanStruk\SmsNotify\SmsNotifyServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SmsNotifyServiceProvider::class,
        ];
    }
}