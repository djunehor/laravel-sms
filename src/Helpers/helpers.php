<?php

use Djunehor\Sms\Concrete\Sms;

if(!function_exists('send_sms')) {
    function send_sms(string $message, string $to, $from = null, string $class = null) {
        $class = $class ? $class : config('laravel-sms.default');
        $sms = new $class($message);
        $sms->to($to);
        if($from)
        $sms->from($from);
        return $sms->send();
    }
}
