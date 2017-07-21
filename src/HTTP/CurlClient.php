<?php

namespace Krixon\MultiFactorAuth\HTTP;

class CurlClient implements Client
{
    public function get(string $url) : string
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL               => $url,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_CONNECTTIMEOUT    => 10,
            CURLOPT_DNS_CACHE_TIMEOUT => 10,
            CURLOPT_TIMEOUT           => 10,
            CURLOPT_SSL_VERIFYPEER    => true,
            CURLOPT_USERAGENT         => 'KrixonMultiFactorAuth',
        ]);

        $data = curl_exec($curl);

        curl_close($curl);

        return $data;
    }
}
