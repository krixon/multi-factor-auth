<?php

namespace Krixon\MultiFactorAuth\OCRA;

use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuth\Hash\AlgorithmProvider;

class Password implements AlgorithmProvider
{
    private $password;
    private $algorithm;


    public function __construct(string $password, Algorithm $algorithm)
    {
        $this->password  = $password;
        $this->algorithm = $algorithm;
    }


    public function __toString() : string
    {
        return 'P' . $this->algorithm;
    }


    public function toMessage() : string
    {
        $hashed = bin2hex(hash($this->algorithm, $this->password, true));

//        switch (true) {
//            case $this->algorithm->is(Algorithm::SHA256):
//                $length = 63;
//                break;
//            case $this->algorithm->is(Algorithm::SHA512):
//                $length = 127;
//                break;
//            case $this->algorithm->is(Algorithm::SHA1):
//            default:
//                $length = 39;
//                break;
//        }
        return pack('H*', $hashed);

//        return pack('H*', str_pad($hashed, $length, '0', STR_PAD_LEFT));
    }


    public function password() : string
    {
        return $this->password;
    }


    public function algorithm() : Algorithm
    {
        return $this->algorithm;
    }



}
