<?php

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class NigerianBulkSms extends Sms
{
    private $baseUrl = 'http://portal.nigeriabulksms.com/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.nigerian_bulk_sms.username');
        $this->password = config('laravel-sms.nigerian_bulk_sms.password');
        if ($message) {
            $this->text($message);
        }
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'apiKey' => $this->username,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
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
            $request = $this->client->get('api', [
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
