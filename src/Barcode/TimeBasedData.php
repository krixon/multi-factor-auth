<?php

namespace Krixon\MultiFactorAuth\Barcode;

use Krixon\MultiFactorAuth\Clock\Clock;
use Krixon\MultiFactorAuth\Hash\Algorithm;

class TimeBasedData extends Data
{
    private $windowLength;
    private $algorithm;


    public function __construct(
        string $secret,
        string $issuer,
        string $accountName,
        int $codeLength = 6,
        int $windowLength = Clock::DEFAULT_WINDOW_LENGTH,
        Algorithm $algorithm = null
    ) {
        parent::__construct($secret, $issuer, $accountName, $codeLength);

        $this->algorithm = $algorithm ?: Algorithm::sha1();

        $this->setWindowLength($windowLength);
    }


    public function windowLength() : int
    {
        return $this->windowLength;
    }


    public function withWindowLength(int $windowLength) : self
    {
        $instance = clone $this;

        $instance->setWindowLength($windowLength);

        return $instance;
    }


    public function algorithm() : Algorithm
    {
        return $this->algorithm;
    }


    public function withAlgorithm(Algorithm $algorithm) : self
    {
        $instance = clone $this;

        $instance->algorithm = $algorithm;

        return $instance;
    }


    private function setWindowLength(int $windowLength) : void
    {
        if ($windowLength <= 0) {
            throw new Exception\InvalidData(sprintf('Window length %d is not greater than 0', $windowLength));
        }

        $this->windowLength = $windowLength;
    }
}
