<?php

namespace Krixon\MultiFactorAuth\Code;

class Code
{
    public const DEFAULT_DIGIT_COUNT = 6;

    private $decimal;


    public function __construct(int $decimal)
    {
        $this->decimal = $decimal;
    }


    public function toDecimal() : int
    {
        return $this->decimal;
    }


    public function toString(int $length = self::DEFAULT_DIGIT_COUNT) : string
    {
        $code = $this->toDecimal() % 10**$length;

        return str_pad($code, $length, '0', STR_PAD_LEFT);
    }


    /**
     * Determines if a string is a valid representation of this code.
     *
     * @param string $code
     *
     * @return bool
     */
    public function equalsString(string $code) : bool
    {
        return $this->toString(strlen($code)) === $code;
    }


    public function __toString()
    {
        return $this->toString();
    }
}
