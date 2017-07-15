<?php

namespace Krixon\MultiFactorAuth\Hash\Exception;

class UnsupportedAlgorithm extends \UnexpectedValueException implements HashException
{
    public function __construct(string $algorithm)
    {
        parent::__construct("Unsupported hash algorithm: $algorithm.");
    }
}
