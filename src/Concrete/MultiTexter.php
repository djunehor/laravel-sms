<?php

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

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
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.78 Safari/537.36 OPR/47.0.2631.39',
            ],
        ]);
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
            $response = $this->client->post('sms', [
                'query' => [
                    'recipients' => implode(',', $this->recipients),
                    'sender_name' => $this->sender ?? config('laravel-sms.sender'),
                    'email' => $this->username,
                    'password' => $this->password,
                    'message' => $this->text,
                ],
            ]);

            $response = $response->getBody()->getContents();
            $this->response = $response['msg'];

            return $esponse['status'] == '1' ? true : false;
        } catch (ClientException $e) {
            Log::info('HTTP Exception in '.__CLASS__.': '.__METHOD__.'=>'.$e->getMessage());
            $this->httpError = $e;

            return false;
        } catch (\Exception $e) {
            Log::info('SMS Exception in '.__CLASS__.': '.__METHOD__.'=>'.$e->getMessage());
            $this->httpError = $e;

            return false;
        }
    }
}
