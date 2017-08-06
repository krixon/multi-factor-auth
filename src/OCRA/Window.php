<?php

namespace Krixon\MultiFactorAuth\OCRA;

class Window
{
    private $offset;
    private $size;


    public function __construct(int $offset, WindowSize $size)
    {
        $this->offset = $offset;
        $this->size   = $size;
    }


    public function __toString() : string
    {
        return $this->size;
    }


    public function toMessage()
    {
        return pack('H*', str_pad(dechex($this->offset), 16, '0', STR_PAD_LEFT));
    }
}
