<?php

namespace Krixon\MultiFactorAuthTests\Unit\OCRA;

use Krixon\MultiFactorAuth\OCRA\ChallengeFormat;
use Krixon\MultiFactorAuthTests\TestCase;

class ChallengeFormatTestOld extends TestCase
{
    /**
     * @dataProvider toStringProvider
     *
     * @param string $format
     * @param int    $length
     * @param string $expected
     */
    public function testToString(string $format, int $length, string $expected)
    {
        $challengeFormat = new ChallengeFormat($format, $length);

        static::assertSame($expected, (string)$challengeFormat);
    }


    public function toStringProvider()
    {
        return [
            ['A', 8, 'QA08'],
            ['N', 8, 'QN08'],
            ['H', 8, 'QH08'],
        ];
    }
}
