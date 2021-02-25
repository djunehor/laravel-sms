<?php
/**
 * Created by PhpStorm.
 * User: Djunehor
 * Date: 5/25/2019
 * Time: 5:51 PM.
 */

namespace Djunehor\Sms\App\Drivers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class SmartSmsSolutions extends Sms
{
    private $baseUrl = 'https://smartsmssolutions.com/api/';

    /**
     * Class Constructor.
     *
     * @param null $message
     */
    public function __construct(string $message = null)
    {
        $this->username = config('laravel-sms.smart_sms.token');
        if ($message) {
            $this->text($message);
        }

        $headers = [
            'Content-Type' => 'Content-type: application/x-www-form-urlencoded',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.78 Safari/537.36 OPR/47.0.2631.39',
        ];

        $this->client = $this->getInstance();
        $this->request = new Request('GET', $this->baseUrl.'json.php', $headers);
    }

    /**
     * @param null $text
     * @return bool
     */
    public function send(string $text = null): bool
    {
        if ($text) {
            $this->setText($text);
        }

        try {
            $response = $this->client->send($this->request, [
                'query' => [
                    'token' => $this->username,
                    'type' => 0,
                    'to' => implode(',', $this->recipients),
                    'sender' => $this->sender ?? config('laravel-sms.sender'),
                    'message' => $this->text,
                    'routing' => 2, //basic route = 2
                ],
            ]);

            $this->response = json_decode($response->getBody()->getContents(), true);

            return array_key_exists('successful', $this->response) ? true : false;
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
