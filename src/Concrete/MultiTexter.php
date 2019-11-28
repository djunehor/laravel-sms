<?php

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class MultiTexter extends Sms
{
    private $baseUrl = 'https://app.multitexter.com/v2/app/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.multitexter.username');
        $this->password = config('laravel-sms.multitexter.password');

        if ($message) {
            $this->text($message);
        }

        $this->client = self::getInstance();
        $this->request = new Request('POST', $this->baseUrl."sms");
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
            $response = $this->client->send($this->request, [
                'form_params' => [
                    'recipients' => implode(',', $this->recipients),
                    'sender_name' => $this->sender ?? config('laravel-sms.sender'),
                    'email' => $this->username,
                    'password' => $this->password,
                    'message' => $this->text,
                ],
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            $this->response = $response['msg'];

            return $response['status'] == '1' ? true : false;
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
