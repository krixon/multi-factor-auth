<?php

namespace Krixon\MultiFactorAuth\OCRA;

use Krixon\MultiFactorAuth\Hash\Algorithm;

class CryptoFunction
{
    private $algorithm;

    private $digits;


    public function __construct(Algorithm $algorithm, int $digitCount)
    {
        if (!($digitCount === 0 || ($digitCount > 3 && $digitCount < 10))) {
            throw new Exception\InvalidDigitCount($digitCount);
        }

        $this->algorithm = $algorithm;
        $this->digits    = $digitCount;
    }


    public function __toString() : string
    {
        return sprintf('HOTP-%s-%d', $this->algorithm, $this->digits);
    }


    public function algorithm() : Algorithm
    {
        return $this->algorithm;
    }


    public function digits() : int
    {
        return $this->digits;
    }
}
