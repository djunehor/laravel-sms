<?php

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class Threegtelecoms extends Sms
{
    private $baseUrl = 'http://sms.3gtelecoms.net/api/send.php';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.threegtelecoms.client_id');
        $this->password = config('laravel-sms.threegtelecoms.password');
        if ($message) {
            $this->text($message);
        }

        $this->client = self::getInstance();
        $this->request = new Request('GET', $this->baseUrl);
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
                'query' => [
                    'ClientID' => $this->username,
                    'Password' => $this->password,
                    'SenderID' => $this->sender ?? config('laravel-sms.threegtelecoms.sender_id'),
                    'MSISDN' => implode(',', $this->recipients),
                    'Msg_Content' => $this->text,
                ],
            ]);
            if ($request->getStatusCode() == 200 ) {
                $this->response = $request->getBody()->getContents();
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
