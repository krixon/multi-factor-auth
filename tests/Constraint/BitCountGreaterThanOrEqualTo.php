<?php

namespace Krixon\MultiFactorAuthTests\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

class BitCountGreaterThanOrEqualTo extends Constraint
{
    private $minBitCount;


    public function __construct(int $minBitCount)
    {
        parent::__construct();

        $this->minBitCount = $minBitCount;
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


    private function countBits(string $string) : int
    {
        // Note we use mb_strlen with the 8bit encoding to ensure that we always get the correct byte count.
        // We cannot reliably use strlen for this because it can be overloaded with mb_strlen.
        return mb_strlen($string, '8bit') * 8;
    }
}
