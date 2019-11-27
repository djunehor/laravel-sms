<?php

namespace Djunehor\Sms\Test;

use Djunehor\Sms\SmsServiceProvider;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Orchestra\Testbench\Concerns\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SmsServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // TODO: Implement getEnvironmentSetUp() method.
        $this->app = $app;
        $smsConfig = include_once __DIR__.'/../src/config/laravel-sms.php';
        $app['config']->set('laravel-sms', $smsConfig);
    }
}
