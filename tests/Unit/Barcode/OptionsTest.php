<?php

namespace Krixon\MultiFactorAuthTests\Unit\Barcode;

use Krixon\MultiFactorAuth\Barcode\Options;
use Krixon\MultiFactorAuthTests\TestCase;

class OptionsTest extends TestCase
{
    public function testWithForegroundColor() : void
    {
        $options = new Options();

        static::assertNotSame('red', $options->foregroundColor());
        static::assertSame('red', $options->withForegroundColor('red')->foregroundColor());
    }


    public function testWithBackgroundColor() : void
    {
        $options = new Options();

        static::assertNotSame('red', $options->backgroundColor());
        static::assertSame('red', $options->withBackgroundColor('red')->backgroundColor());
    }


    public function testWithWidth() : void
    {
        $options = new Options();

        static::assertNotSame(100, $options->width());
        static::assertSame(100, $options->withWidth(100)->width());
    }


    public function testWithHeight() : void
    {
        $options = new Options();

        static::assertNotSame(100, $options->height());
        static::assertSame(100, $options->withHeight(100)->height());
    }


    public function testWithSourceCharset() : void
    {
        $options = new Options();

        static::assertNotSame('utf-8', $options->sourceCharset());
        static::assertSame('utf-8', $options->withSourceCharset('utf-8')->sourceCharset());
    }


    public function testWithTargetCharset() : void
    {
        $options = new Options();

        static::assertNotSame('utf-8', $options->targetCharset());
        static::assertSame('utf-8', $options->withTargetCharset('utf-8')->targetCharset());
    }


    public function testWithErrorCorrectionLevel() : void
    {
        $options = new Options();

        static::assertNotSame('L', $options->errorCorrectionLevel());
        static::assertSame('L', $options->withErrorCorrectionLevel('L')->errorCorrectionLevel());
    }


    public function testWithMargin() : void
    {
        $options = new Options();

        static::assertNotSame(2, $options->margin());
        static::assertSame(2, $options->withMargin(2)->margin());
    }


    public function testWithFormat() : void
    {
        $options = new Options();

        static::assertNotSame('png', $options->format());
        static::assertSame('png', $options->withFormat('png')->format());
    }


    /**
     * @dataProvider validUnionProvider
     *
     * @param Options $lhs
     * @param Options $rhs
     * @param Options $expected
     */
    public function testUnion(Options $lhs, Options $rhs, Options $expected) : void
    {
        static::assertEquals($expected, $lhs->union($rhs));
    }


    /**
     *
     */
    public function validUnionProvider() : array
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


    /**
     * @dataProvider validEqualsProvider
     *
     * @param Options $lhs
     * @param Options $rhs
     * @param bool    $expected
     */
    public function testEquals(Options $lhs, Options $rhs, bool $expected) : void
    {
        static::assertEquals($expected, $lhs->equals($rhs));
    }


    public function validEqualsProvider() : array
    {
        $base = new Options();

        return [
            [$base, $base, true],

            // Width
            [$base, $base->withWidth(200), false],
            [$base->withWidth(100), $base->withWidth(100), true],

            // Height
            [$base, $base->withHeight(200), false],
            [$base->withHeight(100), $base->withHeight(100), true],

            // EC level
            [$base, $base->withErrorCorrectionLevel('M'), false],
            [$base->withErrorCorrectionLevel('L'), $base->withErrorCorrectionLevel('L'), true],

            // Foreground color
            [$base, $base->withForegroundColor('#ffffff'), false],
            [$base->withForegroundColor('#ffffff'), $base->withForegroundColor('#ffffff'), true],

            // Background color
            [$base, $base->withBackgroundColor('#ffffff'), false],
            [$base->withBackgroundColor('#ffffff'), $base->withBackgroundColor('#ffffff'), true],

            // Format
            [$base, $base->withFormat('png'), false],
            [$base->withFormat('png'), $base->withFormat('png'), true],

            // Format
            [$base, $base->withMargin(1), false],
            [$base->withMargin(1), $base->withMargin(1), true],

            [
                $base,
                new class() extends Options
                {

                },
                true
            ],
            [
                new class() extends Options
                {

                },
                $base,
                false
            ],
        ];
    }
}
