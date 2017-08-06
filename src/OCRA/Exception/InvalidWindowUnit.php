<?php

namespace Krixon\MultiFactorAuth\OCRA\Exception;

class InvalidWindowUnit extends \UnexpectedValueException implements OCRAException
{
    public function __construct(int $unit)
    {
        $message = "Invalid window unit $unit: Must be one of {S, M, H}.";

        parent::__construct($message);
    }
}
