<?php

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

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
            $request = $this->client->post('json', [
                'query' => [
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
