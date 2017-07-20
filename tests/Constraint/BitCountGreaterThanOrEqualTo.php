<?php

namespace Krixon\MultiFactorAuthTests\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

class BitCountGreaterThanOrEqualTo extends Constraint
{
    use CountsBits;

    private $minBitCount;


    public function __construct(int $bitCount)
    {
        parent::__construct();

        $this->minBitCount = $bitCount;
    }


    public function toString()
    {
        return sprintf('is at least %s bits', $this->exporter->export($this->minBitCount));
    }


    protected function matches($other)
    {
        return $this->countBits($other) >= $this->minBitCount;
    }


    protected function failureDescription($other)
    {
        return sprintf(
            '%s is at least %s bits (actual bit count: %s)',
            $this->exporter->export($other),
            $this->exporter->export($this->minBitCount),
            $this->exporter->export($this->countBits($other))
        );
    }
}
