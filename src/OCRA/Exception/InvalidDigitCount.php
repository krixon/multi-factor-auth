<?php

namespace Krixon\MultiFactorAuth\OCRA\Exception;

class InvalidDigitCount extends \RangeException implements OCRAException
{
    public function __construct(int $digitCount)
    {
        $message = "Invalid digit count $digitCount: Must be exactly zero or between 4 and 10 inclusive.";

        parent::__construct($message);
    }
}
