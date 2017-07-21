<?php

namespace Krixon\MultiFactorAuthTests\Unit\Code;

use Krixon\MultiFactorAuth\Clock\StoppedClock;
use Krixon\MultiFactorAuth\Code\StandardCodeGenerator;
use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuth\Hash\HashHMACHasher;
use Krixon\MultiFactorAuthTests\TestCase;

class StandardCodeGeneratorTest extends TestCase
{
    /**
     * @dataProvider correctEventBasedCodeProvider
     *
     * @param int    $counter
     * @param string $expectedHex
     * @param int    $expectedDecimal
     * @param string $expectedCode
     */
    public function testGeneratesCorrectEventBasedCode(
        int $counter,
        int $expectedDecimal,
        string $expectedCode
    ) {
        $codec     = new Base32Codec();
        $generator = new StandardCodeGenerator();
        $secret    = $codec->encode('12345678901234567890');
        $code      = $generator->generateEventBasedCode($secret, $counter);

        static::assertSame($expectedDecimal, $code->toDecimal());
        static::assertSame($expectedCode, $code->toString());
    }


    public function correctEventBasedCodeProvider()
    {
        /**
         * Test values are taken from RFC4226 Appendix D (HOTP Algorithm: Test Values).
         *
         * @link https://www.ietf.org/rfc/rfc4226.txt
         */
        return [
            [0, 1284755224, '755224'],
            [1, 1094287082, '287082'],
            [2, 137359152,  '359152'],
            [3, 1726969429, '969429'],
            [4, 1640338314, '338314'],
            [5, 868254676,  '254676'],
            [6, 1918287922, '287922'],
            [7, 82162583,   '162583'],
            [8, 673399871,  '399871'],
            [9, 645520489,  '520489'],
        ];
    }


    /**
     * @dataProvider correctTimeBasedCodeProvider
     * @param int    $time
     * @param string $algorithm
     * @param int    $expectedDecimal
     * @param string $expectedCode
     */
    public function testGeneratesCorrectTimeBasedCode(
        int $time,
        string $algorithm,
        int $expectedDecimal,
        string $expectedCode
    ) {
        $codec     = new Base32Codec();
        $clock     = new StoppedClock($time);
        $hasher    = new HashHMACHasher();
        $generator = new StandardCodeGenerator($clock, $hasher, new Algorithm($algorithm), $codec);

        switch ($algorithm) {
            case Algorithm::SHA1:
                $secret = '12345678901234567890';
                break;
            case Algorithm::SHA256:
                $secret = '12345678901234567890123456789012';
                break;
            case Algorithm::SHA512:
                $secret = '1234567890123456789012345678901234567890123456789012345678901234';
                break;
            default:
                throw new \InvalidArgumentException("Unknown algorithm '$algorithm'.");
        }

        $secret = $codec->encode($secret);
        $code   = $generator->generateTimeBasedCode($secret);

        static::assertSame($expectedDecimal, $code->toDecimal());
        static::assertSame($expectedCode, $code->toString());
    }


    public function correctTimeBasedCodeProvider()
    {
        /**
         * Test values are taken from RFC6238 Appendix B (Test Vectors).
         *
         * @link https://www.ietf.org/rfc/rfc6238.txt
         */
        return [
            [59,          Algorithm::SHA1,   1094287082, '287082'],
            [59,          Algorithm::SHA256, 746119246,  '119246'],
            [59,          Algorithm::SHA512, 490693936,  '693936'],
            [1111111109,  Algorithm::SHA1,   907081804,  '081804'],
            [1111111109,  Algorithm::SHA256, 1568084774, '084774'],
            [1111111109,  Algorithm::SHA512, 225091201,  '091201'],
            [1111111111,  Algorithm::SHA1,   414050471,  '050471'],
            [1111111111,  Algorithm::SHA256, 1167062674, '062674'],
            [1111111111,  Algorithm::SHA512, 1899943326, '943326'],
            [1234567890,  Algorithm::SHA1,   689005924,  '005924'],
            [1234567890,  Algorithm::SHA256, 91819424,   '819424'],
            [1234567890,  Algorithm::SHA512, 1493441116, '441116'],
            [2000000000,  Algorithm::SHA1,   2069279037, '279037'],
            [2000000000,  Algorithm::SHA256, 1790698825, '698825'],
            [2000000000,  Algorithm::SHA512, 1938618901, '618901'],
            [20000000000, Algorithm::SHA1,   1465353130, '353130'],
            [20000000000, Algorithm::SHA256, 777737706,  '737706'],
            [20000000000, Algorithm::SHA512, 1047863826, '863826'],
        ];
    }
}
