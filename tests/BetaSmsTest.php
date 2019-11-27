<?php


namespace Djunehor\Sms\Test;


use Djunehor\Sms\Concrete\BetaSms;

class BetaSmsTest extends TestCase
{

    private $sms;

    public function setUp(): void
    {
        parent::setUp();
        $this->sms = new BetaSms();
        $this->app['config']->set('laravel-sms.beta_sms.username', 'babalola');
        $this->app['config']->set('laravel-sms.beta_sms.password', 'segun');
    }

    public function testConfig()
    {
        $this->assertEquals('babalola', config('laravel-sms.beta_sms.username'));
        $this->assertEquals('segun', config('laravel-sms.beta_sms.password'));
    }

    public function testSendFail()
    {
        $sent = $this->sms->from('Julius')
            ->to('d222d2d2d2')
            ->send();

        $this->assertFalse( $sent);
        $this->assertNotEmpty( $this->sms->getException());
    }

    public function testSendWithHelper()
    {
        $sent = send_sms(BetaSms::class, 'How ar you', '08135087966', 'Omolope');

        $this->assertIsBool( $sent);
    }
}
