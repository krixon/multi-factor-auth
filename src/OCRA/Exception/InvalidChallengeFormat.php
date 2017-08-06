<?php

namespace Krixon\MultiFactorAuth\OCRA\Exception;

class InvalidChallengeFormat extends \UnexpectedValueException implements OCRAException
{
    public function __construct(int $format)
    {
        $message = "Invalid challenge format $format: Must be one of {A, N, H}.";

        parent::__construct($message);
    }
}
