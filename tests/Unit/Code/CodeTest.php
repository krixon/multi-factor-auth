<?php

namespace Krixon\MultiFactorAuthTests\Unit\Code;

use Krixon\MultiFactorAuth\Code\Code;
use Krixon\MultiFactorAuthTests\TestCase;

class CodeTest extends TestCase
{
    /**
     * @dataProvider equalsStringProvider
     *
     * @param int    $code
     * @param string $string
     * @param bool   $expected
     */
    public function testEqualsString(int $code, string $string, bool $expected)
    {
        static::assertSame($expected, (new Code($code))->equalsString($string));
    }


    public function equalsStringProvider()
    {
        return [
            [123456789, '123456789', true],
            [123456789, '23456789', true],
            [123456789, '3456789', true],
            [123456789, '456789', true],
            [123456789, '56789', true],
            [123456789, '6789', true],
            [123456789, '789', true],
            [123456789, '89', true],
            [123456789, '9', true],
            [123456789, '023456789', false],
            [123456789, '003456789', false],
            [123456789, '000456789', false],
            [123456789, '000056789', false],
            [123456789, '000006789', false],
            [123456789, '000000789', false],
            [123456789, '000000089', false],
            [123456789, '000000009', false],
            [123456789, '000000000', false],
            [123456789, '00000000', false],
            [123456789, '0000000', false],
            [123456789, '000000', false],
            [123456789, '00000', false],
            [123456789, '0000', false],
            [123456789, '000', false],
            [123456789, '00', false],
            [123456789, '0', false],
            [123456789, '', false],
        ];
    }


    public function testCastToString()
    {
        static::assertSame('456789', (string)(new Code(123456789)));
    }
}
