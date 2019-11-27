<?php

return [
    'sender' => env('SMS_SENDER', 'MyApp'),
    'beta_sms' => [
        'username' => env('BETASMS_USERNAME'),
        'password' => env('BETASMS_PASSWORD')
    ],
    'bulk_sms_nigeria' => [
        'token' => env('BULK_SMS_NIGERIA_TOKEN'),
        'dnd' => env('BULK_SMS_NIGERIA_DND')
    ],
    'gold_sms_247' => [
        'username' => env('GOLD_SMS_247_USERNAME'),
        'password' => env('GOLD_SMS_247_PASSWORD')
    ],
    'multitexter' => [
        'username' => env('MULTITEXTER_USERNAME'),
        'password' => env('MULTITEXTER_PASSWORD')
    ],
    'smart_sms' => [
        'token' => env('SMART_SMS_TOKEN')
    ],
    'x_wireless' => [
        'api_key' => env('XWIRELESS_API_KEY'),
        'client_id' => env('XWIRELESS_CLIENT_ID')
    ]
];
