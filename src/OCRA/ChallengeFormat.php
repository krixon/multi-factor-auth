<?php

namespace Krixon\MultiFactorAuth\OCRA;

class ChallengeFormat
{
    private const ALPHANUMERIC = 'A';
    private const NUMERIC      = 'N';
    private const HEXADECIMAL  = 'H';

    private $format;

    private $length;


    public function __construct(string $format, int $length)
    {
        if (!in_array($format, [self::ALPHANUMERIC, self::HEXADECIMAL, self::NUMERIC], true)) {
            throw new Exception\InvalidChallengeFormat($format);
        }

        if (!($length > 3 && $length < 65)) {
            throw new Exception\InvalidChallengeLength($length);
        }

        $this->format = $format;
        $this->length = $length;
    }


    public static function fromString(string $string) : self
    {
        return new static($string[1], substr($string, 2));
    }


    public function __toString() : string
    {
        return sprintf('Q%s%02d', $this->format, $this->length);
    }


    public function isAlphaNumeric() : bool
    {
        return self::ALPHANUMERIC === $this->format;
    }

    public function isNumeric() : bool
    {
        return self::NUMERIC === $this->format;
    }

    public function isHexadecimal() : bool
    {
        return self::HEXADECIMAL === $this->format;
    }


    public function length() : int
    {
        return $this->length;
    }
}
