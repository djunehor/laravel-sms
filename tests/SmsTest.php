<?php

namespace Djunehor\Sms\Test;

use Djunehor\Sms\Concrete\BetaSms;

class SmsTest extends TestCase
{
    private $sms;

    public function setUp(): void
    {
        parent::setUp();
        $this->sms = new BetaSms();
    }

    public function testToSingle()
    {
        $number = '08092785634';
        $this->sms->to($number);
        $this->assertEquals([$number], $this->sms->getRecipients());
    }

    public function testToMultiple()
    {
        $this->sms->to('08092785634', '08022334455');
        $this->assertEquals(['08092785634', '08022334455'], $this->sms->getRecipients());
    }

    public function testToArray()
    {
        $numbers = ['08092785634', '08022334455'];
        $this->sms->to($numbers);
        $this->assertEquals($numbers, $this->sms->getRecipients());
    }

    public function testText()
    {
        $text = 'hello world';
        $this->sms->text($text);
        $this->assertEquals($text, $this->sms->getText());
    }

    public function testSender()
    {
        $text = 'Emma';
        $this->sms->from($text);
        $this->assertEquals($text, $this->sms->getSender());
    }

    public function testHasRecipients()
    {
        $this->assertFalse($this->sms->hasRecipients());
        $text = ['08092785634'];
        $this->sms->to($text);
        $this->assertTrue($this->sms->hasRecipients());
    }
}
