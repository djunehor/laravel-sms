<?php

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class Twilio extends Sms
{
   private $baseUrl = 'https://rest.twilio.com/sms/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.twilio.api_secret');
        $this->password = config('laravel-sms.twilio.api_key');
        if ($message) {
            $this->text($message);
        }

        $this->client = self::getInstance();
        $this->request = new Request('POST', $this->baseUrl.'json');
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

            $twilio = new Client($this->username, $this->password);

            $twilio_request = $twilio->messages->create(
                implode(',', $this->recipients),
                [
                    'from' =>  $this->sender ?? config('laravel-sms.sender'),
                    'body' => $this->text
                ]
            );

            if ($twilio_request->accountSid) {
                $this->response = 'The message was sent successfully';

                return true;
            }

            $this->response = $twilio_request->errorMessage;

            return false;
        } catch (TwilioException $e) {
            logger()->error('SMS Exception in '.__CLASS__.': '.__METHOD__.'=>'.$e->getMessage());
            $this->httpError = $e;

            return false;
        }

    }
}
