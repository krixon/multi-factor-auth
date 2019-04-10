<?php

namespace Krixon\MultiFactorAuthTests\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

class ByteCount extends Constraint
{
    use CountsBytes;

    private $byteCount;


    public function __construct(int $byteCount)
    {
        $this->byteCount = $byteCount;
    }


    public function toString() : string
    {
        return sprintf('is exactly %s bytes', $this->exporter()->export($this->byteCount));
    }


    protected function matches($other) : bool
    {
        return $this->countBytes($other) === $this->byteCount;
    }


    protected function failureDescription($other) : string
    {
        return sprintf(
            '%s is exactly %s bytes (actual byte count: %s)',
            $this->exporter()->export(trim(chunk_split(bin2hex($other), 2, ' '))),
            $this->exporter()->export($this->byteCount),
            $this->exporter()->export($this->countBytes($other))
        );
    }
}
