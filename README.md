# Laravel SMS

Laravel SMS allows you to send SMS from your Laravel application using one of 10 sms providers in Nigeria, or your own sms provider.

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
* `SMS_SENDER`
* `BETASMS_USERNAME`
* `BETASMS_PASSWORD`
* `BULK_SMS_NIGERIA_TOKEN`
* `BULK_SMS_NIGERIA_DND`
* `GOLD_SMS_247_USERNAME`
* `GOLD_SMS_247_PASSWORD`
* `MULTITEXTER_USERNAME`
* `MULTITEXTER_PASSWORD`
* `SMART_SMS_TOKEN`
* `XWIRELESS_API_KEY`
* `XWIRELESS_CLIENT_ID`


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
//SmartSmsSolutions
$send = send_sms(SmartSmsSolution::sms, $message, $to)
```

### Available SMS Providers
- [BetaSms](http://login.betasms.com.ng/)
- [BulkSmsNigeria](https://www.bulksmsnigeria.com)
- [GoldSms247](http://goldsms247.com/)
- [MultiTexter](http://www.MultiTexter.com)
- [SmartSmsSolutions](https://smartsmssolutions.com)
- [XWireless](https://secure.xwireless.net)

### Creating custom SMS Provider
- Create a class that extends `Djunehor\Sms\Concrete\Sms` class
- Implement the `send()` which makes the request and return bool
- (Optional) You can add the provider keys to the config/laravel-sms.php

## Contributing
- Fork this project
- Clone to your repo
- Make your changes and run tests `composer test`
- Push and create Pull request
