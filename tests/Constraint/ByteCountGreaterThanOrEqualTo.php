<?php

namespace Krixon\MultiFactorAuthTests\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

class ByteCountGreaterThanOrEqualTo extends Constraint
{
    use CountsBytes;

    private $minByteCount;


    public function __construct(int $byteCount)
    {
        $this->minByteCount = $byteCount;
    }


    public function toString() : string
    {
        return sprintf('is at least %s bytes', $this->exporter()->export($this->minByteCount));
    }


    protected function matches($other) : bool
    {
        return $this->countBytes($other) >= $this->minByteCount;
    }


    protected function failureDescription($other) : string
    {
        return sprintf(
            '%s is at least %s bytes (actual byte count: %s)',
            $this->exporter()->export(trim(chunk_split(bin2hex($other), 2, ' '))),
            $this->exporter()->export($this->minByteCount),
            $this->exporter()->export($this->countBytes($other))
        );
    }
}
