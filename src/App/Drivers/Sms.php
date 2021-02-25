<?php

namespace Djunehor\Sms\App\Drivers;

use GuzzleHttp\Client;

abstract class Sms
{
    protected $text;
    protected $username;
    protected $password;
    protected $recipients = [];
    private static $httpClient;
    protected $sender;
    protected $response;
    protected $client;
    protected $request;

    /**
     * @var \Exception
     */
    public $httpError;

    /**We want HTTP Client instantiated
     * only once in entire app lifecycle
     * @return Client
     */
    public static function getInstance(): Client
    {
        if (! self::$httpClient) {
            self::$httpClient = new Client();
        }

        return self::$httpClient;
    }

    /** Define SMS recipient(s).
     * @param $numbers string|array
     * @return $this
     */
    public function to($numbers): self
    {
        $numbers = is_array($numbers) ? $numbers : func_get_args();

        if (count($numbers)) {
            $this->setRecipients($numbers);
        }

        return $this;
    }

    private function setRecipients($numbers): self
    {
        foreach ($numbers as $number) {
            $this->recipients[] = $this->numberFormat($number);
        }

        return $this;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function text($text = null): self
    {
        if ($text) {
            $this->setText($text);
        }

        return $this;
    }

    private function numberFormat($number): string
    {
        $number = (string) $number;
        $number = trim($number);
        $number = preg_replace("/\s|\+|-/", '', $number);

        return $number;
    }

    public function setText($text): self
    {
        $this->text = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', trim($text));

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function from($from): self
    {
        $this->sender = $from;

        return $this;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getException(): string
    {
        return $this->httpError;
    }

    /**
     * Determines if it has any recipients.
     *
     * @return bool [description]
     */
    public function hasRecipients(): bool
    {
        return property_exists($this, 'recipients') && ! empty($this->recipients);
    }
}
