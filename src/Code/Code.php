<?php

namespace Krixon\MultiFactorAuth\Code;

class Code
{
    public const DEFAULT_DIGIT_COUNT = 6;

    private $binary;
    private $decimal;
    private $hex;


    public function __construct(string $hash)
    {
        $offset = ord(substr($hash, -1)) & 0xF;

        $this->decimal = (
            ((ord($hash[$offset])     & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) <<  8) |
            ( ord($hash[$offset + 3]) & 0xFF)
        );
    }


    public function toBinary() : string
    {
        if (null === $this->binary) {
            $this->binary = decbin($this->decimal);
        }

        return $this->binary;
    }


    public function toHex() : string
    {
        if (null === $this->hex) {
            $this->hex = dechex($this->decimal);
        }

        return $this->hex;
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


    public function __toString()
    {
        return $this->toString();
    }
}
