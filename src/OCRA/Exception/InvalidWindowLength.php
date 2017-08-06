<?php

namespace Krixon\MultiFactorAuth\OCRA\Exception;

class InvalidWindowLength extends \RangeException implements OCRAException
{
    public function __construct(int $length, string $unit, int $min, int $max)
    {
        $message = "Invalid window length $length for unit $unit: Must be between $min and $max inclusive.";

        parent::__construct($message);
    }
}
