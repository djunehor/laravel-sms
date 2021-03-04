<?php
/**
 * Created by PhpStorm.
 * User: Djunehor
 * Date: 1/22/2019
 * Time: 9:36 AM.
 */

namespace Djunehor\Sms\Concrete;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class BulkSmsNigeria extends Sms
{
    private $baseUrl = 'https://www.bulksmsnigeria.com/api/v1/sms/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.bulk_sms_nigeria.token');

        if ($message) {
            $this->text($message);
        }

        $this->client = self::getInstance();
        $this->request = new Request('POST', $this->baseUrl.'create');
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
            $response = $this->client->send($this->request, [
                'form_params' => [
                    'api_token' => config('laravel-sms.bulk_sms_nigeria.token'),
                    'to' => implode(',', $this->recipients),
                    'from' => $this->sender ?? config('laravel-sms.sender'),
                    'body' => $this->text,
                    'dnd' => config('laravel-sms.bulk_sms_nigeria.dnd'),
                ],
            ]);

            $response = json_decode($response->getBody()->getContents(), true) ?? [];
            $this->response = array_key_exists('error', $response) ? $response['error']['message'] : $response['data']['message'];

            return $response['data']['status'] == 'success' ? true : false;
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
