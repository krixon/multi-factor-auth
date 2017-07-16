<?php

namespace Krixon\MultiFactorAuth\Barcode;

use Krixon\MultiFactorAuth\Code\Code;

abstract class Data
{
    private $secret;
    private $accountName;
    private $issuer;
    private $digitCount;


    public function __construct(
        string $secret,
        string $issuer,
        string $accountName,
        int $digitCount = Code::DEFAULT_DIGIT_COUNT
    ) {
        $this->secret       = $secret;
        $this->digitCount   = $digitCount;

        $this->setAccountName($accountName);
        $this->setIssuer($issuer);
    }


    public function secret() : string
    {
        return $this->secret;
    }


    public function withSecret(string $secret)
    {
        $instance = clone $this;

        $instance->secret = $secret;

        return $instance;
    }


    public function issuer() : string
    {
        return $this->issuer;
    }


    public function withIssuer(string $issuer)
    {
        $instance = clone $this;

        $instance->setIssuer($issuer);

        return $instance;
    }


    public function accountName() : string
    {
        return $this->accountName;
    }


    public function withAccountName(string $accountName)
    {
        $instance = clone $this;

        $instance->setAccountName($accountName);

        return $instance;
    }


    public function digitCount() : int
    {
        return $this->digitCount;
    }


    public function withDigitCount(int $digitCount)
    {
        $instance = clone $this;

        $instance->digitCount = $digitCount;

        return $instance;
    }


    private function setAccountName(string $accountName) : void
    {
        if (strpos($accountName, ':') !== false) {
            throw new Exception\InvalidData('Colon is not allowed in account name');
        }

        $this->accountName = $accountName;
    }


    private function setIssuer(?string $issuer) : void
    {
        if (null !== $issuer && strpos($issuer, ':') !== false) {
            throw new Exception\InvalidData('Colon is not allowed in issuer');
        }

        $this->issuer = $issuer;
    }
}
