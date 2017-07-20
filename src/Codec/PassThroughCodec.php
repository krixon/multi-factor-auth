<?php

namespace Krixon\MultiFactorAuth\Codec;

class PassThroughCodec implements Codec
{
    public function encode(string $data) : string
    {
        return $data;
    }


    public function decode(string $data) : string
    {
        return $data;
    }
}
