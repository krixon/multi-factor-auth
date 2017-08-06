<?php

namespace Krixon\MultiFactorAuthTests\Unit\Utility;

use Krixon\MultiFactorAuth\Utility\BaseConverter;
use Krixon\MultiFactorAuthTests\TestCase;

class BaseConverterTest extends TestCase
{
    /**
     * @dataProvider correctConversionProvider
     *
     * @param int    $fromBase
     * @param int    $toBase
     * @param string $number
     * @param string $expected
     */
    public function testEncodesCorrectly(int $fromBase, int $toBase, string $number, string $expected)
    {
        // Must be able to convert to the expected base...
        $converted = BaseConverter::convert($number, $fromBase, $toBase);

        // ...and back again without any loss of precision.
        $restored  = BaseConverter::convert($converted, $toBase, $fromBase);

        static::assertSame($expected, $converted);
        static::assertSame($number, $restored);
    }


    public function correctConversionProvider()
    {
        return [
            [10, 16, '0', '0'],
            [10, 16, '1', '1'],
            [10, 16, '15', 'F'],
            [10, 16, '16', '10'],
            [10, 16, '16', '10'],
            [16, 36, 'ABCDEF00001234567890', '3O47RE02JZQISVIO'],
            [16, 36, 'ABCDEF01234567890123456789ABCDEF', 'A65XA07491KF5ZYFPVBO76G33'],
            [2, 10, '1000', '8'],
            [
                16,
                2,
                '70B1D707EAC2EDF4C6389F440C7294B51FFF57BB',
                '111000010110001110101110000011111101010110000101110' .
                '110111110100110001100011100010011111010001000000110' .
                '001110010100101001011010100011111111111110101011110' .
                '111011'
            ],
            [
                16,
                10,
                '70B1D707EAC2EDF4C6389F440C7294B51FFF57BB',
                '643372930067913326838082478477533553256088688571'
            ],
        ];
    }
}
