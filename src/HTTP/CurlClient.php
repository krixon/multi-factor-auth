<?php

namespace Krixon\MultiFactorAuth\HTTP;

class CurlClient implements Client
{
    private $verify;


    public function __construct(bool $verifySSL = true)
    {
        $this->verify = $verifySSL;
    }


    public function get(string $url) : string
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL               => $url,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_CONNECTTIMEOUT    => 10,
            CURLOPT_DNS_CACHE_TIMEOUT => 10,
            CURLOPT_TIMEOUT           => 10,
            CURLOPT_SSL_VERIFYPEER    => $this->verify,
            CURLOPT_USERAGENT         => 'KrixonMultiFactorAuth',
        ]);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }
}
