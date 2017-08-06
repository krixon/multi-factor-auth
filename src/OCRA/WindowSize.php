<?php

namespace Krixon\MultiFactorAuth\OCRA;

class WindowSize
{
    private const SECONDS = 'S';
    private const MINUTES = 'M';
    private const HOURS   = 'H';

    private $length;
    private $unit;


    public function __construct(int $length, string $unit)
    {
        if (!in_array($unit, [self::SECONDS, self::MINUTES, self::HOURS], true)) {
            throw new Exception\InvalidWindowUnit($unit);
        }

        $min = $unit === self::HOURS ? 0 : 1;
        $max = $unit === self::HOURS ? 48 : 59;

        if (!($length >= $min && $length <= $max)) {
            throw new Exception\InvalidWindowLength($length, $unit, $min, $max);
        }

        $this->length = $length;
        $this->unit   = $unit;
    }


    public static function fromString(string $string)
    {
        return new static(...sscanf($string, 'T%d%s'));
    }


    public function __toString() : string
    {
        return sprintf('T%d%s', $this->length, $this->unit);
    }
}
