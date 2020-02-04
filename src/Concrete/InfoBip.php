<?php

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class InfoBip extends Sms
{
    private $baseUrl;

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.infobip.username');
        $this->password = config('laravel-sms.infobip.password');
        $this->baseUrl = config('laravel-sms.infobip.base_url', 'http://infobio.com/');
        if ($message) {
            $this->text($message);
        }
        $headers = [
            'Authorization' => 'Basic '.base64_encode("$this->username:$this->password"),
            'Content-Type' => 'application/json',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.78 Safari/537.36 OPR/47.0.2631.39',
        ];

        $this->client = self::getInstance();
        $this->request = new Request('POST', $this->baseUrl.'/sms/2/text/single', $headers);
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
            $request = $this->client->send($this->request, [
                'form_params' => [
                    'from' => $this->sender ?? config('laravel-sms.sender'),
                    'to' => implode(',', $this->recipients),
                    'text' => $this->text,
                ],
            ]);

            $response = json_decode($request->getBody()->getContents(), true);
            $this->response = $response;

            return $request->getStatusCode() == 200 ? true : false;
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
