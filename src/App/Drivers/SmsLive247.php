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

class SmsLive247 extends Sms
{
    private $baseUrl = 'http://www.smslive247.com/http/';
    private $messageType = false;

    /**
     * Class Constructor.
     *
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.smslive247.token');

        if ($message) {
            $this->text($message);
        }

        $this->client = self::getInstance();
        $this->request = new Request('GET', $this->baseUrl.'index.aspx');
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
        if ($text) {
            $this->setText($text);
        }

        // get sessionID

        try {
            $response = $this->client->send($this->request, [
                'query' => [
                    'cmd' => 'login',
                    'owner_email' => config('laravel-sms.smslive247.owner_email'),
                    'subacct' => config('laravel-sms.smslive247.subacct_username'),
                    'subacctpwd' => config('laravel-sms.smslive247.subacct_password'),
                ],
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
            $this->httpError = $exception;

            return false;
        }
        try {
            $response = $this->client->send($this->request, [
                'query' => [
                    'cmd' => 'sendmsg',
                    'sessionid' => $sessionId,
                    'sendto' => implode(',', $this->recipients),
                    'sender' => $this->sender ?? config('laravel-sms.sender'),
                    'message' => $this->text,
                    'msgtype' => (int) $this->messageType,
                ],
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            $split = explode(':', $response);
            $this->response = last($split);

            return $split[0] == 'OK' ? true : false;
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
