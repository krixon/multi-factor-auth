<?php

namespace Krixon\MultiFactorAuth\OCRA;

use Krixon\MultiFactorAuth\CountsBytes;

class SessionData
{
    use CountsBytes;

    private $data;


    public function __construct(string $data)
    {
        $this->data = $data;
    }


    public function __toString() :string
    {
        return sprintf('S%03d', $this->length());
    }


    public function toMessage() : string
    {
        return pack('H*', str_pad($this->data, $this->length() * 2, '0', STR_PAD_LEFT));
    }


    public function length() : int
    {
        return $this->countBytes($this->data);
    }
}
