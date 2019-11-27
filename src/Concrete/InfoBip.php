<?php


namespace Djunehor\Sms\Concrete;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class InfoBip extends Sms
{
    private $baseUrl;

    /**
     * Class Constructor
     * @param null $message
     */
    public function __construct($message = null)
    {
        $this->username = config('laravel-sms.infobip.username');
        $this->password = config('laravel-sms.infobip.password');
        $this->baseUrl = config('laravel-sms.infobip.base_url');
        if ($message) {
            $this->text($message);
        };
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            "headers" => [
                'Authorization' => "Basic ".base64_encode("$this->username:$this->password"),
                "Content-Type" => 'application/json',
                'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.78 Safari/537.36 OPR/47.0.2631.39"
            ]
        ]);
    }

    /**
     * @param null $text
     * @return bool
     */
    public function send($text = null): bool
    {
        if ($text) $this->setText($text);
        try {
            $request = $this->client->post("/sms/2/text/single", [
                "form_params" => [
                    "from" => $this->sender ?? config('laravel-sms.sender'),
                    "to" => join(',', $this->recipients),
                    "text" => $this->text,
                ]
            ]);

            $response = json_decode($request->getBody()->getContents(), true);
            $this->response = $response;
            return $request->getStatusCode() == 200 ? true : false;
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
