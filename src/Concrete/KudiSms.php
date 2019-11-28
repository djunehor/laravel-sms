<?php

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class KudiSms extends Sms
{
    private $baseUrl = 'https://account.kudisms.net/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.kudi_sms.username');
        $this->password = config('laravel-sms.kudi_sms.password');
        if ($message) {
            $this->text($message);
        }
        $headers = [
                'apiKey' => $this->username,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.78 Safari/537.36 OPR/47.0.2631.39',
            ];
        $this->client = self::getInstance();
        $this->request = new Request('GET', $this->baseUrl."api/", $headers);
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
                'query' => [
                    'username' => $this->username,
                    'password' => $this->password,
                    'sender' => $this->sender ?? config('laravel-sms.sender'),
                    'mobiles' => implode(',', $this->recipients),
                    'message' => $this->text,
                ],
            ]);

            $response = json_decode($request->getBody()->getContents(), true);

            if (isset($response['status']) && $response['status'] == 'OK') {
                return true;
            }

            $this->response = $response['error'];

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
