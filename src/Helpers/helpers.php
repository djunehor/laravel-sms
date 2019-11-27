<?php

use Djunehor\Sms\Concrete\Sms;

if(!function_exists('send_sms')) {
    function send_sms($class, string $message, string $to, $from = null) {
        $sms = new $class();
        $sms->to($to);
        if($from)
        $sms->from($from);
        return $sms->send();
    }
}
