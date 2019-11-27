<?php

namespace Djunehor\Sms\Concrete;

use Djunehor\Sms\Contracts\SmsServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class MultiTexter extends Sms
{
    private $baseUrl = 'http://www.MultiTexter.com/tools/geturl/';
    /**
     * Class Constructor
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.multitexter.username');
        $this->password = config('laravel-sms.multitexter.password');
        if ($message) {
            $this->text($message);
        };
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            "headers" => [
                'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.78 Safari/537.36 OPR/47.0.2631.39"
            ]
        ]);
    }

    /**
     * @param null $text
     * @return bool
     */
    public function send($text = null) : bool
    {
        if ($text) $this->setText($text);
        try {
            $response = $this->client->get('Sms.php', [
                "query" => [
                    "recipients" => join(',', $this->recipients),
                    "sender" => $this->sender ?? config('laravel-sms.sender'),
                    "username" => $this->username,
                    "password" => $this->password,
                    "message" => $this->text,
                ]
            ]);
        } catch (ClientException $e) {
            Log::info('SMS Client Exception: '. json_encode($e->getMessage()));
            $this->httpError = $e;
            return false;
        } catch (\Exception $e) {
            Log::info('SMS Exception: '. json_encode($e->getMessage()));
            $this->httpError = $e;
            return false;
        }
        $this->response = $response->getBody()->getContents();
        return $this->response == "100" ? true : false;
    }
}
