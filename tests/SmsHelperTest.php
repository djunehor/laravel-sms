<?php

namespace Djunehor\Sms\Test;

use Djunehor\Sms\App\Drivers\MultiTexter;
use Djunehor\Sms\App\Drivers\Nexmo;

class SmsHelperTest extends TestCase
{
    private $sms;

    public function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('laravel-sms.default', MultiTexter::class);
        $this->app['config']->set('laravel-sms.beta_sms.username', 'babalola');
        $this->app['config']->set('laravel-sms.beta_sms.password', 'segun');
    }

    public function testConfig()
    {
        $this->assertEquals('babalola', config('laravel-sms.beta_sms.username'));
        $this->assertEquals('segun', config('laravel-sms.beta_sms.password'));
        $this->assertEquals('segun', config('laravel-sms.beta_sms.password'));
        $this->assertEquals(MultiTexter::class, config('laravel-sms.default'));
    }

    public function testSendWithHelper()
    {
        $sent = $this->send_sms('How are you', '08022334455', 'Omolope');

        $this->assertIsBool($sent);
    }

    public function testSendWithHelperSpecifyClass()
    {
        $sent = $this->send_sms('How are you', '08022334455', 'Omolope', Nexmo::class);

        $this->assertIsBool($sent);
    }

    protected function send_sms(string $message, string $to, $from = null, string $class = null)
    {
        $class = $class ? $class : config('laravel-sms.default');
        $sms = new $class($message);
        $sms->to($to);
        if ($from) {
            $sms->from($from);
        }

        return $sms->send();
    }
}
