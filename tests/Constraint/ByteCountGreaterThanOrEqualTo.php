<?php

namespace Krixon\MultiFactorAuthTests\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

class ByteCountGreaterThanOrEqualTo extends Constraint
{
    use CountsBytes;

    private $minByteCount;


    public function __construct(int $byteCount)
    {
        parent::__construct();

        $this->minByteCount = $byteCount;
    }


    public function toString()
    {
        return sprintf('is at least %s bytes', $this->exporter->export($this->minByteCount));
    }


    protected function matches($other)
    {
        return $this->countBytes($other) >= $this->minByteCount;
    }


    protected function failureDescription($other)
    {
        return sprintf(
            '%s is at least %s bytes (actual byte count: %s)',
            $this->exporter->export(trim(chunk_split(bin2hex($other), 2, ' '))),
            $this->exporter->export($this->minByteCount),
            $this->exporter->export($this->countBytes($other))
        );
    }
}
