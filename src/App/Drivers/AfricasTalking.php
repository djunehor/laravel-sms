<?php

namespace Djunehor\Sms\App\Drivers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class AfricasTalking extends Sms
{
    private $baseUrl = 'https://api.africastalking.com/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        if ($message) {
            $this->text($message);
        }
        $this->client = $this->getInstance();
        $headers = [
            'apiKey' => config('laravel-sms.africas_talking.api_key'),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        $this->request = new Request('POST', $this->baseUrl.'version1/messaging', $headers);
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
            $request = $this->client->send($this->request, [
                'form_params' => [
                    'username' => config('laravel-sms.africas_talking.username', 'djunehor'),
                    'from' => $this->sender ?? config('laravel-sms.sender'),
                    'to' => implode(',', $this->recipients),
                    'message' => $this->text,
                ],
            ]);

            $response = json_decode($request->getBody()->getContents(), true);

            if (! empty($response['SMSMessageData']['Recipients'])) {
                $this->response = $response['SMSMessageData']['Message'];

                return true;
            }

            $this->response = $response['SMSMessageData']['Message'];

            return false;
        } catch (ClientException $e) {
            logger()->error('HTTP Exception in '.__CLASS__.': '.__METHOD__.'=>'.$e->getMessage());
            $this->httpError = $e;

            return false;
        } catch (\Exception $e) {
            logger()->error('SMS Exception in '.__CLASS__.': '.__METHOD__.'=>'.$e->getMessage());
            $this->httpError = $e;

            return false;
        }
    }
}
