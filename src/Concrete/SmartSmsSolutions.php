<?php
/**
 * Created by PhpStorm.
 * User: Djunehor
 * Date: 5/25/2019
 * Time: 5:51 PM.
 */

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class SmartSmsSolutions extends Sms
{
    private $baseUrl = 'https://smartsmssolutions.com/api/';

    /**
     * Class Constructor.
     *
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.smart_sms.token');
        if ($message) {
            $this->text($message);
        }

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'header' => 'Content-type: application/x-www-form-urlencoded',
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

        $routing = 2; //basic route = 2
        $type = 0;
        $token = $this->username;

        try {
            $response = $this->client->get('json.php', [
                'query' => [
                    'token' => $token,
                    'type' => $type,
                    'to' => implode(',', $this->recipients),
                    'sender' => $this->sender ?? config('laravel-sms.sender'),
                    'message' => $this->text,
                    'routing' => $routing,
                ],
            ]);

            $this->response = json_decode($response->getBody()->getContents(), true);

            return array_key_exists('successful', $this->response) ? true : false;
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
