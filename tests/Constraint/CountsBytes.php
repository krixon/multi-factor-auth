<?php

namespace Krixon\MultiFactorAuthTests\Constraint;

trait CountsBytes
{
    protected function countBytes(string $string) : int
    {
        // Note we use mb_strlen with the 8bit encoding to ensure that we always get the correct byte count.
        // We cannot reliably use strlen for this because it can be overloaded with mb_strlen.
        return mb_strlen($string, '8bit');
    }
}
