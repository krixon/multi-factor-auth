<?php

namespace Krixon\MultiFactorAuth\Codec;

interface Codec
{
    public function encode(string $data) : string;
    public function decode(string $data) : string;
}
