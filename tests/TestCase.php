<?php

namespace Krixon\MultiFactorAuthTests;

use Krixon\MultiFactorAuthTests\Constraint;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function assertByteCount(int $expectedByteCount, string $string, string $message = '') : void
    {
        static::assertThat($string, new Constraint\ByteCount($expectedByteCount), $message);
    }


    public static function assertByteCountGreaterThanOrEqualTo(
        int $minByteCount,
        string $string,
        string $message = ''
    ) : void {
        static::assertThat($string, new Constraint\ByteCountGreaterThanOrEqualTo($minByteCount), $message);
    }
}
