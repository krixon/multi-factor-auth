<?php

namespace Krixon\MultiFactorAuth\Barcode;

class EventBasedData extends Data
{
    private $counter;


    public function __construct(
        string $secret,
        string $issuer,
        string $accountName,
        int $digitCount = 6,
        int $counter = 0
    ) {
        parent::__construct($secret, $issuer, $accountName, $digitCount);

        $this->setCounter($counter);
    }


    public function counter() : int
    {
        return $this->counter;
    }


    public function withCounter(int $counter)
    {
        $instance = clone $this;

        $instance->setCounter($counter);

        return $instance;
    }


    private function setCounter(int $counter) : void
    {
        if ($counter < 0) {
            throw new Exception\InvalidData(sprintf('Counter %d is not greater than or equal to 0', $counter));
        }

        $this->counter = $counter;
    }
}
