<?php
/**
 * Created by PhpStorm.
 * User: Djunehor
 * Date: 1/22/2019
 * Time: 9:36 AM.
 */

namespace Djunehor\Sms\App\Drivers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class BetaSms extends Sms
{
    private $baseUrl = 'http://login.betasms.com.ng/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct(string $message = null)
    {
        $this->username = config('laravel-sms.beta_sms.username');
        $this->password = config('laravel-sms.beta_sms.password');

        if ($message) {
            $this->text($message);
        }

        $this->client = $this->getInstance();
        $this->request = new Request('POST', $this->baseUrl.'api/');
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
                'form_params' => [
                    'mobiles' => implode(',', $this->recipients),
                    'sender' => $this->sender ?? config('laravel-sms.sender'),
                    'username' => $this->username,
                    'password' => $this->password,
                    'message' => $this->text,
                ],
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            $this->response = array_key_exists('status', $response) ? $response['status'] : $response['error'];

            return $this->response == 'OK';
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
