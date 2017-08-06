<?php

namespace Krixon\MultiFactorAuth\OCRA\Exception;

class InvalidChallengeLength extends \RangeException implements OCRAException
{
    public function __construct(int $length)
    {
        $message = "Invalid challenge length $length: Must be between 4 and 64 inclusive.";

        parent::__construct($message);
    }
}
