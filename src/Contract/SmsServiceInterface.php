<?php
/**
 * Created by PhpStorm.
 * User: djunehor
 * Date: 26/09/2018
 * Time: 3:47 PM.
 */

namespace Djunehor\Sms\Contracts;

use Djunehor\Sms\Concrete\Sms;

/**
 * Interface SmsServiceInterface.
 */
interface SmsServiceInterface
{
    /**
     * @param array|mixed $numbers
     * @return array
     */
    public function to($numbers) : self;

    /**
     * @param $text
     * @return $this | string
     */
    public function text($text = null) : self;

    /**
     * @param $from
     * @return string
     */
    public function from(string $from) : self;

    /**
     * @return mixed
     */
    public function getResponse(): string;

    /**
     * @return \Exception|null
     */
    public function getException() : Sms;

    /**
     * @param null $text
     * @return bool
     */
    public function send($text = null): bool;
}
