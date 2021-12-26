<?php
return [
    /*
     * Клієнт за замовчуванням
    */
    'default' => 'log',

    /**
     * Відладка
    */
    'debug' => false,

    'drivers' => [
        'log' => [
            'client' => \RomanStruk\SmsNotify\Clients\Log\Log::class
        ],
        'viber' => [
            'client' => \RomanStruk\SmsNotify\Clients\ViberUa\Viber::class,
            'token' => '',
            'sender_vb' => '',
            'sender_sms' => '',
            'default_channel' => 'viber',
        ],
        'mts-communicator' => [
            'client' => \RomanStruk\SmsNotify\Clients\MtsCommunicator\MtsCommunicator::class,
            'login' => '',
            'password' => '',
            'client_id' => '',
            'alfa_name' => '',
        ],
        'turbosms' => [
            'client' => \RomanStruk\SmsNotify\Clients\TurboSms\TurboSms::class,
            'token' => '',
            'sender_sms' => 'TurboSMS'
        ]
    ],
    'map' => [
        'ua' => 'log',
        'by' => 'mts-communicator'
    ]
];