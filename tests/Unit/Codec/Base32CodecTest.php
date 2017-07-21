<?php

namespace Krixon\MultiFactorAuthTests\Unit\Codec;

use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\Codec\Exception\DecodingFailed;
use Krixon\MultiFactorAuthTests\TestCase;

class Base32CodecTest extends TestCase
{
    /**
     * @dataProvider correctEncodingProvider
     *
     * @param string $input
     * @param string $expected
     * @param bool   $pad
     */
    public function testEncodesCorrectly(string $input, string $expected, bool $pad = false)
    {
        $output = (new Base32Codec($pad))->encode($input);

        static::assertSame($expected, $output);
    }


    public function correctEncodingProvider()
    {
        return [
            ['', ''],
            ['a', 'ME======', true],
            ['abcd', 'MFRGGZA=', true],
            ['foo', 'MZXW6'],
            ['foo', 'MZXW6===', true],
            [
                'A longer string, but will it still encode?',
                'IEQGY33OM5SXEIDTORZGS3THFQQGE5LUEB3WS3DMEBUXIIDTORUWY3BAMVXGG33EMU7Q'
            ],
            [
                'A longer string, but will it still encode?',
                'IEQGY33OM5SXEIDTORZGS3THFQQGE5LUEB3WS3DMEBUXIIDTORUWY3BAMVXGG33EMU7Q====',
                true
            ],
            [
                '123 Numbers and !Punctuation$=',
                'GEZDGICOOVWWEZLSOMQGC3TEEAQVA5LOMN2HKYLUNFXW4JB5'
            ],
            [
                '123 Numbers and !Punctuation$=',
                'GEZDGICOOVWWEZLSOMQGC3TEEAQVA5LOMN2HKYLUNFXW4JB5',
                true
            ],
        ];
    }


    /**
     * @dataProvider correctDecodingProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testDecodesCorrectly(string $input, string $expected)
    {
        $output = (new Base32Codec())->decode($input);

        static::assertSame($expected, $output);
    }


    public function correctDecodingProvider()
    {
        return array_map(function (array $args) {
            if (count($args) === 3) {
                array_pop($args);
            }
            return array_reverse($args);
        }, $this->correctEncodingProvider());
    }


    public function testThrowsExpectedExceptionWhenDecodingInvalidBase32Data()
    {
        $this->expectException(DecodingFailed::class);

        (new Base32Codec())->decode('a');
    }
}
