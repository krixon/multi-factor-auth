<?php

namespace Krixon\MultiFactorAuthTests\Unit\Code;

use InvalidArgumentException;
use Krixon\MultiFactorAuth\Clock\StoppedClock;
use Krixon\MultiFactorAuth\Code\StandardCodeGenerator;
use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuthTests\TestCase;

class StandardCodeGeneratorTest extends TestCase
{
    /**
     * @dataProvider correctEventBasedCodeProvider
     *
     * @param int    $counter
     * @param string $expected
     */
    public function testGeneratesCorrectEventBasedCode(int $counter, string $expected) : void
    {
        $codec     = new Base32Codec();
        $generator = new StandardCodeGenerator();
        $secret    = $codec->encode('12345678901234567890');
        $code      = $generator->generateEventBasedCode($secret, $counter);

        static::assertSame($expected, $code);
    }


    public function correctEventBasedCodeProvider() : array
    {
        /**
         * Test values are taken from RFC4226 Appendix D (HOTP Algorithm: Test Values).
         *
         * @link https://www.ietf.org/rfc/rfc4226.txt
         */
        return [
            [0, '755224'],
            [1, '287082'],
            [2, '359152'],
            [3, '969429'],
            [4, '338314'],
            [5, '254676'],
            [6, '287922'],
            [7, '162583'],
            [8, '399871'],
            [9, '520489'],
        ];
    }


    /**
     * @dataProvider correctTimeBasedCodeProvider
     *
     * @param int    $time
     * @param string $algorithm
     * @param string $expected
     */
    public function testGeneratesCorrectTimeBasedCode(int $time, string $algorithm, string $expected) : void
    {
        $codec     = new Base32Codec();
        $clock     = new StoppedClock($time);
        $generator = new StandardCodeGenerator($clock, new Algorithm($algorithm), $codec);

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
                throw new InvalidArgumentException("Unknown algorithm '$algorithm'.");
        }

        $secret = $codec->encode($secret);
        $code   = $generator->generateTimeBasedCode($secret);

        static::assertSame($expected, $code);
    }


    public function correctTimeBasedCodeProvider() : array
    {
        /**
         * Test values are taken from RFC6238 Appendix B (Test Vectors).
         *
         * @link https://www.ietf.org/rfc/rfc6238.txt
         */
        return [
            [59,          Algorithm::SHA1,   '287082'],
            [59,          Algorithm::SHA256, '119246'],
            [59,          Algorithm::SHA512, '693936'],
            [1111111109,  Algorithm::SHA1,   '081804'],
            [1111111109,  Algorithm::SHA256, '084774'],
            [1111111109,  Algorithm::SHA512, '091201'],
            [1111111111,  Algorithm::SHA1,   '050471'],
            [1111111111,  Algorithm::SHA256, '062674'],
            [1111111111,  Algorithm::SHA512, '943326'],
            [1234567890,  Algorithm::SHA1,   '005924'],
            [1234567890,  Algorithm::SHA256, '819424'],
            [1234567890,  Algorithm::SHA512, '441116'],
            [2000000000,  Algorithm::SHA1,   '279037'],
            [2000000000,  Algorithm::SHA256, '698825'],
            [2000000000,  Algorithm::SHA512, '618901'],
            [20000000000, Algorithm::SHA1,   '353130'],
            [20000000000, Algorithm::SHA256, '737706'],
            [20000000000, Algorithm::SHA512, '863826'],
        ];
    }
}
