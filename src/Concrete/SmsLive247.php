<?php
/**
 * Created by PhpStorm.
 * User: Djunehor
 * Date: 5/25/2019
 * Time: 5:51 PM
 */

namespace Djunehor\Sms\Concrete;


use Djunehor\Sms\Concrete\Sms;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class SmsLive247 extends Sms
{

    private $baseUrl = 'http://www.smslive247.com/http/';
    private $messageType = false;

    /**
     * Class Constructor
     *
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.smslive247.token');
        if ($message) {
            $this->text($message);
        };

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            "headers" => [
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.78 Safari/537.36 OPR/47.0.2631.39"
            ]
        ]);
    }

    public function type(bool $type)
    {
        $this->messageType = $type;
        return $this;
    }

    /**
     * @param null $text
     * @return bool
     */
    public function send($text = null): bool
    {
        if ($text) $this->setText($text);

        // get sessionID
        try {
            $response = $this->client->get('index.aspx', [
                "query" => [
                    "cmd" => 'login',
                    "owner_email" => config('laravel-sms.smslive247.owner_email'),
                    "subacct" => config('laravel-sms.smslive247.subacct_username'),
                    "subacctpwd" => config('laravel-sms.smslive247.subacct_password'),
                ]
            ]);

            $response = json_decode($response->getBody()->getContents(), true);

            $split = explode(':', $response);

            if ($split[0] == 'OK' && isset($split[1])) {
                $sessionId = trim($split[1]);
            } else {
                $this->response = last($split);
                return false;
            }
        } catch (\Exception $exception) {
            $this->httpError = $e;
            return false;
        }
        try {
            $response = $this->client->get('index.aspx', [
                "query" => [
                    "cmd" => "sendmsg",
                    "sessionid" => $sessionId,
                    "sendto" => join(',', $this->recipients),
                    "sender" => $this->sender ?? config('laravel-sms.sender'),
                    "message" => $this->text,
                    "msgtype" => (int)$this->messageType
                ]
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            $split = explide(":", $response);
            $this->response = last($split);
            return $split[0] == 'OK' ? true : false;
        } catch (ClientException $e) {
            Log::info('HTTP Exception in ' . __CLASS__ . ': ' . __METHOD__ . '=>' . $e->getMessage());
            $this->httpError = $e;
            return false;
        } catch (\Exception $e) {
            Log::info('SMS Exception in ' . __CLASS__ . ': ' . __METHOD__ . '=>' . $e->getMessage());
            $this->httpError = $e;
            return false;
        }
    }
}
