<?php

namespace Krixon\MultiFactorAuthTests\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

class BitCount extends Constraint
{
    use CountsBits;

    private $bitCount;


    public function __construct(int $bitCount)
    {
        parent::__construct();

        $this->bitCount = $bitCount;
    }


    public function toString()
    {
        return sprintf('is exactly %s bits', $this->exporter->export($this->bitCount));
    }


    protected function matches($other)
    {
        return $this->countBits($other) === $this->bitCount;
    }


    protected function failureDescription($other)
    {
        return sprintf(
            '%s is exactly %s bits (actual bit count: %s)',
            $this->exporter->export($other),
            $this->exporter->export($this->bitCount),
            $this->exporter->export($this->countBits($other))
        );
    }
}
