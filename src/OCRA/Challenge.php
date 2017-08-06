<?php

namespace Krixon\MultiFactorAuth\OCRA;

use Krixon\MultiFactorAuth\Utility\BaseConverter;

class Challenge
{
    private $challenge;

    private $format;


    public function __construct(string $challenge, ChallengeFormat $format)
    {
        $this->challenge = $challenge;
        $this->format    = $format;
    }


    public static function fromString(string $string) : self
    {
        return static::generate(ChallengeFormat::fromString($string));
    }


    public static function generate(ChallengeFormat $format) : self
    {
        $digitCount = $format->length();
        $number     = '';

        do {
            try {
                $number .= random_int(0, PHP_INT_MAX);
            } catch (\Exception $e) {
                throw new Exception\ChallengeGenerationFailed($e);
            }
        } while (strlen($number) < $digitCount);

        $challenge = substr($number, 0, $digitCount);

        if ($format->isHexadecimal()) {
            $challenge = BaseConverter::convert($challenge, 10, 16);
        } elseif ($format->isAlphaNumeric()) {
            $challenge = BaseConverter::convert($challenge, 10, 36);
        }

        return new Challenge($challenge, $format);
    }


    public function __toString() : string
    {
        return $this->format;
    }


    public function toMessage() : string
    {
        return pack('H*', str_pad($this->challenge, 255, '0'));
    }
}
