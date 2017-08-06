<?php

namespace Krixon\MultiFactorAuth\OCRA;

class OCRASuite
{
    private $version;

    private $cryptoFunction;

    private $dataInput;


    public function __construct(int $version, CryptoFunction $cryptoFunction, DataInput $dataInput)
    {
        $this->version        = $version;
        $this->cryptoFunction = $cryptoFunction;
        $this->dataInput      = $dataInput;
    }


    public function toMessage() : string
    {
        return $this . pack('H*', '00') . $this->dataInput->toMessage();
    }


    public function __toString() : string
    {
        return sprintf(
            'OCRA-%d:%s:%s',
            $this->version,
            $this->cryptoFunction,
            $this->dataInput
        );
    }


    public function cryptoFunction() : CryptoFunction
    {
        return $this->cryptoFunction;
    }


    public function dataInput() : DataInput
    {
        return $this->dataInput;
    }
}
