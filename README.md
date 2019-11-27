# Laravel SMS
[![CircleCI](https://circleci.com/gh/djunehor/laravel-sms.svg?style=svg)](https://circleci.com/gh/djunehor/laravel-sms)
[![Latest Stable Version](https://poser.pugx.org/djunehor/laravel-sms/v/stable)](https://packagist.org/packages/djunehor/laravel-sms)
[![Total Downloads](https://poser.pugx.org/djunehor/laravel-sms/downloads)](https://packagist.org/packages/djunehor/laravel-sms)
[![License](https://poser.pugx.org/djunehor/laravel-sms/license)](https://packagist.org/packages/djunehor/laravel-sms)

Laravel SMS allows you to send SMS from your Laravel application using one of over 10 sms providers, or your own sms provider.

- [Laravel SMS](#laravel-sms)
    - [Installation](#installation)
        - [Laravel 5.5 and above](#laravel-55-and-above)
        - [Laravel 5.4 and older](#laravel-54-and-older)
        - [Lumen](#lumen)
        - [Env Keys](#env-keys)
    - [Usage](#usage)
        - [All parts of speech](#using-helper-function)
    - [Available SMS Providers](#available-sms-providers)
    - [Creating custom SMS Provider](#creating-custom-sms-provider)
    - [Contributing](#contributing)

## Installation

### Step 1
You can install the package via composer:

```shell
composer require djunehor/laravel-sms
```

#### Laravel 5.5 and above

The package will automatically register itself, so you can start using it immediately.

#### Laravel 5.4 and older

In Laravel version 5.4 and older, you have to add the service provider in `config/app.php` file manually:

```php
'providers' => [
    // ...
    Djunehor\Sms\SmsServiceProvider::class,
];
```
#### Lumen

After installing the package, you will have to register it in `bootstrap/app.php` file manually:
```php
// Register Service Providers
    // ...
    $app->register(Djunehor\Sms\SmsServiceProvider::class);
];
```

#### Env Keys
```dotenv
SMS_SENDER=

BETASMS_USERNAME=
BETASMS_PASSWORD=

BULK_SMS_NIGERIA_TOKEN=
BULK_SMS_NIGERIA_DND=

GOLD_SMS_247_USERNAME=
GOLD_SMS_247_PASSWORD=

MULTITEXTER_USERNAME=
MULTITEXTER_PASSWORD=

SMART_SMS_TOKEN=

XWIRELESS_API_KEY=
XWIRELESS_CLIENT_ID=

NEXMO_API_KEY=
NEXMO_API_SECRET=

RING_CAPTCHA_APP_KEY=
RING_CAPTCHA_API_KEY=
RING_CAPTCHA_APP_SECRET=

AFRICASTALKING_API_KEY=
AFRICASTALKING_USERNAME=

NIGERIAN_BULK_SMS_USERNAME=
NIGERIAN_BULK_SMS_PASSWORD=

KUDI_SMS_USERNAME=
KUDI_SMS_PASSWORD=

MEBO_SMS_API_KEY=

SMSLIVE247_TOKEN=
```


### Step 2 - Publishing files
Run:
`php artisan vendor:publish --tag=laravel-sms`
This will move the migration file, seeder file and config file to your app. You can set your sms details in the config file or via env

### Step 3 - Adding SMS credentials
- Add the env keys to your `.env` file
- Or edit the config/laravel-sms.php file


## Usage
```php
//using betaSMS
use Djunehor\Sms\BetaSms;`

$sms = new BetaSms();
$sms->text($message)->to(08135087966)->from('MyLaravel')->send();
```

### Using Helper function
```php
//MeboSms
$send = send_sms($message, $to, $from, MeboSms::class);
```
The default SMS provider is Nexmo. You can set the default SMS provider in `config/laravel-sms.php` e.g ` 'default' => \Djunehor\Sms\Concrete\SmartSmsSolutions::class,`, so you can use the helper function like this:
```php
$send = send_sms($message, $to);
//$from is optional and is better set in the config
```

### Available SMS Providers
|Provider|URL|Tested|
|:--------- | :-----------------: | :------: |
|Nexmo|https://developer.nexmo.com/api/sms#send-an-sms|Yes|
|AfricasTalking|https://build.at-labs.io/docs/sms%2Fsending|Yes||
|BetaSms|https://login.betasms.com.ng/|Yes|
|MultiTexter|https://web.multitexter.com/MultiTexter_HTTP_SMS_API%202.0.pdf|Yes|
|BulkSmsNigeria|https://www.bulksmsnigeria.com/bulk-sms-api|Yes|
|GoldSms247|https://goldsms247.com/index.php/api|Yes|
|KudiSms|https://kudisms.net/api/|Yes|
|Mebosms|http://mebosms.com/api-sms|Yes|
|NigerianBulkSms|https://nigeriabulksms.com/sms-gateway-api/|Yes|
|SmartSmsSolutions|https://docs.smartsmssolutions.com/docs/send-with-basic-route|Yes|
|RingCaptcha|https://my.ringcaptcha.com/docs/api|No|
|SmsLive247|http://portal.smslive247.com/developer_api/http.aspx|No|
|XWireless|https://xwireless.net/cportal/knowledge-base/article/sms-3|No|

### Creating custom SMS Provider
- Create a class that extends `Djunehor\Sms\Concrete\Sms` class
- Implement the `send()` which makes the request and return bool
- (Optional) You can add the provider keys to the config/laravel-sms.php

## Contributing
- Fork this project
- Clone to your repo
- Make your changes and run tests `composer test`
- Push and create Pull request
