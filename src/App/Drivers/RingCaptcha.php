<?php

namespace Djunehor\Sms\App\Drivers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class RingCaptcha extends Sms
{
    private $baseUrl = 'https://api.ringcaptcha.com/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct(string $message = null)
    {
        $this->username = config('laravel-sms.ring_captcha.app_key');
        $this->password = config('laravel-sms.ring_captcha.api_key');
        if ($message) {
            $this->text($message);
        }
        $this->client = self::getInstance();
        $this->request = new Request('POST', $this->baseUrl."$this->username/sms");
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
                'form_params' => [
                    'phone' => implode(',', $this->recipients),
                    'app_key' => $this->username,
                    'api_key' => $this->password,
                    'message' => $this->text,
                ],
            ]);

            $response = json_decode($request->getBody()->getContents(), true);

            if ($response['status'] == 'SUCCESS') {
                $this->response = 'The message was sent successfully';

                return true;
            }

            $this->response = $response['message'];

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
