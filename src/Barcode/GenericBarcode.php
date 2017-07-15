<?php

namespace Krixon\MultiFactorAuth\Barcode;

class GenericBarcode implements Barcode
{
    private $imageData;

    private $mimeType;


    public function __construct(string $imageData, string $mimeType)
    {
        $this->imageData = $imageData;
        $this->mimeType  = $mimeType;
    }


    public function imageData() : string
    {
        return $this->imageData;
    }


    public function mimeType() : string
    {
        return $this->mimeType;
    }


    public function dataUri() : string
    {
        return sprintf(
            'data:%s;base64,%s',
            $this->mimeType(),
            base64_encode($this->imageData())
        );
    }
}
