<?php
return [
    /*
     * Клієнт за замовчуванням
    */
    'default' => 'log',

    'drivers' => [
        'log' => [
            'client' => \RomanStruk\SmsNotify\Clients\Log::class
        ],
        'viber' => [
            'client' => ''
        ]
    ],
];