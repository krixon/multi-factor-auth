<?php

namespace Krixon\MultiFactorAuth\Utility\Exception;

use Krixon\MultiFactorAuth\Exception\MultiFactorAuthException;

class InvalidNumberBase extends \RangeException implements MultiFactorAuthException
{
    public function __construct(int $base, int $min, int $max)
    {
        parent::__construct("Invalid number base $base: Must be between $min and $max inclusive.");
    }
}
