<?php

namespace Krixon\MultiFactorAuth\Barcode;

use Krixon\MultiFactorAuth\Clock\Clock;
use Krixon\MultiFactorAuth\Code\Code;
use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuth\Hash\Exception\UnsupportedAlgorithm;

class TimeBasedData extends Data
{
    private $windowLength;
    private $algorithm;


    public function __construct(
        string $secret,
        string $issuer,
        string $accountName,
        int $digitCount = Code::DEFAULT_DIGIT_COUNT,
        int $windowLength = Clock::DEFAULT_WINDOW_LENGTH,
        string $algorithm = Algorithm::SHA1
    ) {
        parent::__construct($secret, $issuer, $accountName, $digitCount);

        $this->setWindowLength($windowLength);
        $this->setAlgorithm($algorithm);
    }


    public function windowLength() : int
    {
        return $this->windowLength;
    }


    public function withWindowLength(int $windowLength)
    {
        $instance = clone $this;

        $instance->setWindowLength($windowLength);

        return $instance;
    }


    public function algorithm() : string
    {
        return $this->algorithm;
    }


    public function withAlgorithm(string $algorithm)
    {
        $instance = clone $this;

        $instance->setAlgorithm($algorithm);

        return $instance;
    }


    private function setWindowLength(int $windowLength) : void
    {
        if ($windowLength <= 0) {
            throw new Exception\InvalidData(sprintf('Window length %d is not greater than 0', $windowLength));
        }

        $this->windowLength = $windowLength;
    }


    private function setAlgorithm(string $algorithm) : void
    {
        try {
            Algorithm::assertSupported($algorithm);
        } catch (UnsupportedAlgorithm $e) {
            throw new Exception\InvalidData('Unsupported algorithm', $e);
        }

        $this->algorithm = $algorithm;
    }
}
