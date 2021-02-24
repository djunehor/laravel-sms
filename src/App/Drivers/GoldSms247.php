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

class GoldSms247 extends Sms
{
    private $responseCodes = [
        'OK' => 'Successful',

        '2904' => 'SMS Sending Failed',

        '2905' => 'Invalid username/password combination',

        '2906' => 'Credit exhausted',

        '2907' => 'Gateway unavailable',

        '2908' => 'Invalid schedule date format',

        '2909' => 'Unable to schedule',

        '2910' => 'Username is empty',

        '2911' => 'Password is empty',

        '2912' => 'Recipient is empty',

        '2913=Message is empty',

        '2914=Sender is empty',

        '2915' => 'One or more required fields are empty',
    ];

    private $baseUrl = 'http://goldsms247.com/components/com_spc/';

    /**
     * Class Constructor.
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.gold_sms_247.username');
        $this->password = config('laravel-sms.gold_sms_247.password');

        if ($message) {
            $this->text($message);
        }

        $this->client = self::getInstance();
        $this->request = new Request('GET', $this->baseUrl.'smsapi.php');
    }

    public function getResponse()
    {
        $split = explode(' ', $this->response);

        return array_key_exists($split[0], $this->responseCodes) ? $this->responseCodes[$split[0]] : '';
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
                'query' => [
                    'username' => $this->username,
                    'password' => $this->password,
                    'recipient' => implode(',', $this->recipients),
                    'sender' => $this->sender ?? config('laravel-sms.sender'),
                    'message' => $this->text,
                ],
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            $split = explode(' ', $response);
            $this->response = array_key_exists($split[0], $this->responseCodes) ? $this->responseCodes[$split[0]] : '';

            return (! $split[0] || $split[0] == 'OK') ? true : false;
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
