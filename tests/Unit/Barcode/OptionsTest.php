<?php

namespace Krixon\MultiFactorAuthTests\Unit\Barcode;

use Krixon\MultiFactorAuth\Barcode\Options;
use Krixon\MultiFactorAuthTests\TestCase;

class OptionsTest extends TestCase
{
    /**
     * @dataProvider validUnionProvider
     *
     * @param Options $lhs
     * @param Options $rhs
     * @param Options $expected
     */
    public function testUnion(Options $lhs, Options $rhs, Options $expected)
    {
        $result = $lhs->union($rhs);

        static::assertEquals($expected, $result);
    }


    /**
     *
     */
    public function validUnionProvider()
    {
        $base = new Options();

        return [
            [$base, $base, $base],

            // Width
            [$base, $base->withWidth(200), $base->withWidth(200)],
            [$base->withWidth(100), $base->withWidth(200), $base->withWidth(100)],

            // Height
            [$base, $base->withHeight(200), $base->withHeight(200)],
            [$base->withHeight(100), $base->withHeight(200), $base->withHeight(100)],

            // EC level
            [$base, $base->withErrorCorrectionLevel('M'), $base->withErrorCorrectionLevel('M')],
            [
                $base->withErrorCorrectionLevel('L'),
                $base->withErrorCorrectionLevel('M'),
                $base->withErrorCorrectionLevel('L'),
            ],

            // Foreground color
            [$base, $base->withForegroundColor('#ffffff'), $base->withForegroundColor('#ffffff')],
            [
                $base->withForegroundColor('#ffffff'),
                $base->withForegroundColor('#000000'),
                $base->withForegroundColor('#ffffff'),
            ],

            // Background color
            [$base, $base->withBackgroundColor('#ffffff'), $base->withBackgroundColor('#ffffff')],
            [
                $base->withBackgroundColor('#ffffff'),
                $base->withBackgroundColor('#000000'),
                $base->withBackgroundColor('#ffffff'),
            ],

            // Format
            [$base, $base->withFormat('png'), $base->withFormat('png')],
            [$base->withFormat('png'), $base->withFormat('svg'), $base->withFormat('png')],

            // Format
            [$base, $base->withMargin(1), $base->withMargin(1)],
            [$base->withMargin(1), $base->withMargin(2), $base->withMargin(1)],
        ];
    }
}
