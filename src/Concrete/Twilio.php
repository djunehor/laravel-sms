<?php

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class Twilio extends Sms
{

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.twilio.account_sid');
        $this->password = config('laravel-sms.twilio.auth_token');
        if ($message) {
            $this->text($message);
        }
        $this->client = new Client($this->username,$this->password );
    }

    /**
     * @param null $text
     * @return bool
     */
    public function send($text = null): bool
    {

        if ($text) {
            $this->setText($text);
        }
        try {

            $request = $this->client->messages->create(implode(',', $this->recipients),[
                    'from' => $this->sender ?? config('laravel-sms.sender'),
                    'body' => $this->text
            ]);

            if ($request->sid) {
                $this->response = 'The message was sent successfully';
                return true;
            }

            $this->response = $request->errorMessage;
            return false;

        } catch (TwilioException $e) {
            logger()->error('SMS Exception in '.__CLASS__.': '.__METHOD__.'=>'.$e->getMessage());
            $this->httpError = $e;
            return false;
        }

    }
}
