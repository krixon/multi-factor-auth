<?php

namespace Krixon\MultiFactorAuth\Barcode;

use Krixon\MultiFactorAuth\HTTP\Client;

abstract class HTTPGenerator implements BarcodeGenerator
{
    private $httpClient;


    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }


    public function generateBarcode(Data $data, Options $options = null) : Barcode
    {
        $options  = $options ?: Options::default();
        $url      = $this->url($data, $options);
        $data     = $this->get($url);
        $mimeType = $this->mimeType($options);

        return new GenericBarcode($data, $mimeType);
    }


    abstract protected function url(Data $data, Options $options) : string;
    abstract protected function mimeType(Options $options) : string;


    private function get(string $url) : string
    {
        return $this->httpClient->get($url);
    }
}
