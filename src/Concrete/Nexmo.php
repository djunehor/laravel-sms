<?php

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class Nexmo extends Sms
{
    private $baseUrl = 'https://rest.nexmo.com/sms/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.nexmo.api_key');
        $this->password = config('laravel-sms.nexmo.api_secret');
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
            $request = $this->client->send($this->request, [
                'form_params' => [
                    'to' => implode(',', $this->recipients),
                    'from' => $this->sender ?? config('laravel-sms.sender'),
                    'api_key' => $this->username,
                    'api_secret' => $this->password,
                    'text' => $this->text,
                ],
            ]);

            $response = json_decode($request->getBody()->getContents(), true);

            if ($response['messages'][0]['status'] == 0) {
                $this->response = 'The message was sent successfully';

                return true;
            }

            $this->response = $response['messages'][0]['error-text'];

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
