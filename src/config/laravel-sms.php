<?php

return [
    'default' => \Djunehor\Sms\Concrete\MultiTexter::class,
    'sender' => env('SMS_SENDER', 'MyApp'),
    'beta_sms' => [
        'username' => env('BETASMS_USERNAME'),
        'password' => env('BETASMS_PASSWORD'),
    ],
    'bulk_sms_nigeria' => [
        'token' => env('BULK_SMS_NIGERIA_TOKEN'),
        'dnd' => env('BULK_SMS_NIGERIA_DND', 2),
    ],
    'gold_sms_247' => [
        'username' => env('GOLD_SMS_247_USERNAME'),
        'password' => env('GOLD_SMS_247_PASSWORD'),
    ],
    'multitexter' => [
        'username' => env('MULTITEXTER_USERNAME'),
        'password' => env('MULTITEXTER_PASSWORD'),
    ],
    'smart_sms' => [
        'token' => env('SMART_SMS_TOKEN'),
    ],
    'x_wireless' => [
        'api_key' => env('XWIRELESS_API_KEY'),
        'client_id' => env('XWIRELESS_CLIENT_ID'),
    ],
    'nexmo' => [
        'api_key' => env('NEXMO_API_KEY'),
        'api_secret' => env('NEXMO_API_SECRET'),
    ],
    'smslive247' => [
        'token' => env('SMSLIVE247_TOKEN'),
    ],
    'ring_captcha' => [
        'app_key' => env('RING_CAPTCHA_APP_KEY'),
        'app_secret' => env('RING_CAPTCHA_APP_SECRET'),
        'api_key' => env('RING_CAPTCHA_API_KEY'),
    ],
    'africas_talking' => [
        'api_key' => env('AFRICASTALKING_API_KEY'),
        'username' => env('AFRICASTALKING_USERNAME'),
    ],
    'nigerian_bulk_sms' =>[
        'username' => env('NIGERIAN_BULK_SMS_USERNAME'),
        'password' => env('NIGERIAN_BULK_SMS_PASSWORD'),
    ],
    'kudi_sms' => [
        'username' => env('KUDI_SMS_USERNAME'),
        'password' => env('KUDI_SMS_PASSWORD'),
    ],
    'mebo_sms' => [
        'api_key' => env('MEBO_SMS_API_KEY'),
        'dnd' => env('MEBO_SMS_DND', 0),
    ],
];
