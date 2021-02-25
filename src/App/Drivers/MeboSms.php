<?php

namespace Djunehor\Sms\App\Drivers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class MeboSms extends Sms
{
    private $baseUrl = 'http://mebosms.com/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct(string $message = null)
    {
        $this->username = config('laravel-sms.mebo_sms.api_key');
        if ($message) {
            $this->text($message);
        }

        $this->client = self::getInstance();
        $this->request = new Request('GET', $this->baseUrl.'api');
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
            $request = $this->client->send($this->request, [
                'query' => [
                    'apikey' => $this->username,
                    'sender' => $this->sender ?? config('laravel-sms.sender'),
                    'destination' => implode(',', $this->recipients),
                    'mssg' => $this->text,
                    'dnd' => config('laravel-sms.mebo_sms.dnd'),
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
