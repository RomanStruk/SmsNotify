<?php
namespace RomanStruk\SmsNotify\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use RomanStruk\SmsNotify\SmsNotifyFacade;
use RomanStruk\SmsNotify\SmsNotifyServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SmsNotifyServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'SmsNotifyFacade' => SmsNotifyFacade::class
        ];
    }

    protected function getSendSuccessJson($client)
    {
        return file_get_contents(__DIR__ . '/responses/'.$client.'_send_success.json');
    }

    protected function getResponseJson(string $file)
    {
        return file_get_contents(__DIR__ . '/responses/'.$file.'.json');
    }
}