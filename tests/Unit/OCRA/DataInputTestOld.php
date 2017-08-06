<?php

namespace Krixon\MultiFactorAuthTests\Unit\OCRA;

use Krixon\MultiFactorAuth\OCRA\DataInput;
use Krixon\MultiFactorAuthTests\TestCase;

class DataInputTestOld extends TestCase
{
    /**
     * @dataProvider toStringProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testToString(string $input, string $expected = null)
    {
        $expected = $expected ?? $input;

        static::assertSame($expected, (string)DataInput::fromString($expected));
    }


    public function toStringProvider()
    {
        return [
            ['C-QN08-PSHA1'],
            ['QA10-T1M'],
            ['QH08-S512'],
            ['QH8-S512', 'QH08-S512'],
        ];
    }
}
