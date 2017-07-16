<?php

namespace Krixon\MultiFactorAuth\Barcode;

use Krixon\MultiFactorAuth\HTTP\Client;

abstract class HTTPGenerator implements BarcodeGenerator
{
    use SupportsDefaultOptions,
        GeneratesKeyURIsFromData;

    private $httpClient;


    public function __construct(Client $httpClient, Options $defaults = null)
    {
        $this->httpClient = $httpClient;
        $this->defaults   = $defaults;
    }


    public function generateBarcode(Data $data, Options $options = null) : Barcode
    {
        $options  = $this->resolveOptions($options);
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
