<?php

namespace Krixon\MultiFactorAuth\Clock\Exception;

class InvalidWindowLength extends \RangeException implements ClockException
{
    public function __construct(int $length)
    {
        $message = "Invalid window length $length: Must be greater than zero.";

        parent::__construct($message);
    }
}
