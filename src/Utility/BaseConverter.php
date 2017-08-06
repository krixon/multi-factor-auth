<?php

namespace Krixon\MultiFactorAuth\Utility;

class BaseConverter
{
    /**
     * Converts a number between arbitrary bases.
     *
     * Supports base 2 up to base 36 inclusive.
     *
     * Although PHP provides the base_convert() function for this purpose, it loses precision on large numbers. This
     * converter instead relies on the bcmath extension to provide support for string-based numbers of arbitrary length.
     *
     *                 base-16                 base-32              base-16
     * base_convert(): ABCDEF00001234567890 => 3O47RE02JZSW0KS8  => ABCDEF00001240000000
     * BaseConverter:  ABCDEF00001234567890 => 33O47RE02JZQISVIO => ABCDEF00001234567890
     *
     * @param string $number   The number to convert as a string.
     * @param int    $fromBase The base of $number. Must be between 2 and 36 inclusive.
     * @param int    $toBase   The base to which $number should be converted. Must be between 2 and 36 inclusive.
     *
     * @return string
     * @throws Exception\InvalidNumberBase if either $fromBase or $toBase are not between 2 and 36 inclusive.
     */
    public static function convert(string $number, int $fromBase, int $toBase)
    {
        foreach ([$fromBase, $toBase] as $base) {
            if (!($base > 1 && $base < 37)) {
                throw new Exception\InvalidNumberBase($base, 2, 36);
            }
        }

        $number = trim($number);

        if ('' === $number || '0' === $number) {
            return '0';
        }

        if ('1' === $number) {
            return '1';
        }

        if ($fromBase !== 10) {
            $length   = strlen($number);
            $quotient = 0;
            for ($i = 0; $i < $length; $i++) {
                $remainder = base_convert($number[$i], $fromBase, 10);
                $quotient  = bcadd(bcmul($quotient, $fromBase), $remainder);
            }
        } else {
            $quotient = $number;
        }

        if ($toBase !== 10) {
            $result = '';
            while (bccomp($quotient, '0', 0) > 0) {
                $remainder = bcmod($quotient, $toBase);
                $result    = base_convert($remainder, 10, $toBase) . $result;
                $quotient  = bcdiv($quotient, $toBase, 0);
            }
        } else {
            $result = $quotient;
        }

        return strtoupper($result);
    }
}
