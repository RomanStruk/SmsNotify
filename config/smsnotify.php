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
            'client' => \RomanStruk\SmsNotify\Clients\Log::class
        ],
        'viber' => [
            'client' => '',
            'token' => '',
            'sender_vb' => '',
            'sender_sms' => '',
            'default_channel' => 'viber',
        ],
        'mts-communicator' => [
            'client' => ''
        ]
    ],
    'map' => [
        'ua' => 'log',
        'by' => 'mts-communicator'
    ]
];