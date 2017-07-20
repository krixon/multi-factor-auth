<?php

namespace Krixon\MultiFactorAuthTests;

use Krixon\MultiFactorAuthTests\Constraint;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function assertBitCount(int $expectedBitCount, string $string, string $message = '')
    {
        static::assertThat($string, new Constraint\BitCount($expectedBitCount), $message);
    }


    public static function assertBitCountGreaterThanOrEqualTo(int $minBitCount, string $string, string $message = '')
    {
        static::assertThat($string, new Constraint\BitCountGreaterThanOrEqualTo($minBitCount), $message);
    }
}
