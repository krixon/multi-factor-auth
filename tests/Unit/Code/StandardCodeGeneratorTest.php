<?php

namespace Krixon\MultiFactorAuthTests\Unit\Code;

use Krixon\MultiFactorAuth\Clock\StoppedClock;
use Krixon\MultiFactorAuth\Code\StandardCodeGenerator;
use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuth\OCRA\Challenge;
use Krixon\MultiFactorAuth\OCRA\ChallengeFormat;
use Krixon\MultiFactorAuth\OCRA\CryptoFunction;
use Krixon\MultiFactorAuth\OCRA\DataInput;
use Krixon\MultiFactorAuth\OCRA\OCRASuite;
use Krixon\MultiFactorAuth\OCRA\Password;
use Krixon\MultiFactorAuth\OCRA\Window;
use Krixon\MultiFactorAuth\OCRA\WindowSize;
use Krixon\MultiFactorAuthTests\TestCase;

class StandardCodeGeneratorTest extends TestCase
{
    /**
     * @dataProvider correctEventBasedCodeProvider
     *
     * @param int    $counter
     * @param string $expected
     */
    public function testGeneratesCorrectEventBasedCode(int $counter, string $expected)
    {
        $codec     = new Base32Codec();
        $generator = new StandardCodeGenerator();
        $secret    = $codec->encode('12345678901234567890');
        $code      = $generator->generateEventBasedCode($secret, $counter);

        static::assertSame($expected, $code);
    }


    public function correctEventBasedCodeProvider()
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
    public function testGeneratesCorrectTimeBasedCode(int $time, string $algorithm, string $expected)
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
                throw new \InvalidArgumentException("Unknown algorithm '$algorithm'.");
        }

        $secret = $codec->encode($secret);
        $code   = $generator->generateTimeBasedCode($secret);

        static::assertSame($expected, $code);
    }


    public function correctTimeBasedCodeProvider()
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


    /**
     * @dataProvider correctOCRACodeProvider
     *
     * @param int       $secretByteCount
     * @param OCRASuite $suite
     * @param string    $expectedCode
     */
    public function testGeneratesCorrectOCRACode(int $secretByteCount, OCRASuite $suite, string $expectedCode)
    {
        $codec     = new Base32Codec();
        $generator = new StandardCodeGenerator();

        switch ($secretByteCount) {
            case 20:
                $secret = '12345678901234567890';
                break;
            case 32:
                $secret = '12345678901234567890123456789012';
                break;
            case 64:
                $secret = '1234567890123456789012345678901234567890123456789012345678901234';
                break;
            default:
                throw new \InvalidArgumentException("Unsupported secret byte count $secretByteCount.");
        }

        $secret = $codec->encode($secret);
        $code   = $generator->generateOCRACode($secret, $suite, $suite->cryptoFunction()->digits());

        static::assertSame($expectedCode, $code);
    }


    public function correctOCRACodeProvider()
    {
        /**
         * Test values are taken from RFC6287 Appendix C (Test Vectors).
         *
         * @link https://tools.ietf.org/html/rfc6287#appendix-C
         */

        $data = [];

        // OCRA-1:HOTP-SHA1-6:QN08

        $tests = [
            '00000000' => '237653',
            '11111111' => '243178',
            '22222222' => '653583',
            '33333333' => '740991',
            '44444444' => '608993',
            '55555555' => '388898',
            '66666666' => '816933',
            '77777777' => '224598',
            '88888888' => '750600',
            '99999999' => '294470',
        ];

        foreach ($tests as $q => $expected) {
            $data[] = [
                20,
                new OCRASuite(
                    1,
                    new CryptoFunction(Algorithm::sha1(), 6),
                    new DataInput(
                        new Challenge(
                            dechex($q),
                            ChallengeFormat::fromString('QN08')
                        )
                    )
                ),
                $expected
            ];
        }

        // OCRA-1:HOTP-SHA256-8:C-QN08-PSHA1

        $tests = [
            '0' => '65347737',
            '1' => '86775851',
            '2' => '78192410',
            '3' => '71565254',
            '4' => '10104329',
            '5' => '65983500',
            '6' => '70069104',
            '7' => '91771096',
            '8' => '75011558',
            '9' => '08522129',
        ];

        foreach ($tests as $c => $expected) {
            $data[] = [
                32,
                new OCRASuite(
                    1,
                    new CryptoFunction(Algorithm::sha256(), 8),
                    new DataInput(
                        new Challenge(
                            dechex('12345678'),
                            ChallengeFormat::fromString('QN08')
                        ),
                        new Password('1234', Algorithm::sha1()),
                        null,
                        $c
                    )
                ),
                $expected
            ];
        }

        // OCRA-1:HOTP-SHA256-8:QN08-PSHA1

        $tests = [
            '00000000' => '83238735',
            '11111111' => '01501458',
            '22222222' => '17957585',
            '33333333' => '86776967',
            '44444444' => '86807031',
        ];

        foreach ($tests as $q => $expected) {
            $data[] = [
                32,
                new OCRASuite(
                    1,
                    new CryptoFunction(Algorithm::sha256(), 8),
                    new DataInput(
                        new Challenge(
                            dechex($q),
                            ChallengeFormat::fromString('QN08')
                        ),
                        new Password('1234', Algorithm::sha1())
                    )
                ),
                $expected
            ];
        }

        // OCRA-1:HOTP-SHA512-8:C-QN08

        $tests = [
            '07016083' => ['00000', '00000000'],
            '63947962' => ['00001', '11111111'],
            '70123924' => ['00002', '22222222'],
            '25341727' => ['00003', '33333333'],
            '33203315' => ['00004', '44444444'],
            '34205738' => ['00005', '55555555'],
            '44343969' => ['00006', '66666666'],
            '51946085' => ['00007', '77777777'],
            '20403879' => ['00008', '88888888'],
            '31409299' => ['00009', '99999999'],
        ];

        foreach ($tests as $expected => $cq) {
            $data[] = [
                64,
                new OCRASuite(
                    1,
                    new CryptoFunction(Algorithm::sha512(), 8),
                    new DataInput(
                        new Challenge(
                            dechex($cq[1]),
                            ChallengeFormat::fromString('QN08')
                        ),
                        null,
                        null,
                        $cq[0]
                    )
                ),
                $expected
            ];
        }

        // OCRA-1:HOTP-SHA512-8:QN08-T1M

        $tests = [
             '00000000' => '95209754',
             '11111111' => '55907591',
             '22222222' => '22048402',
             '33333333' => '24218844',
             '44444444' => '36209546',
        ];

        foreach ($tests as $q => $expected) {
            $data[] = [
                64,
                new OCRASuite(
                    1,
                    new CryptoFunction(Algorithm::sha512(), 8),
                    new DataInput(
                        new Challenge(
                            dechex($q),
                            ChallengeFormat::fromString('QN08')
                        ),
                        null,
                        null,
                        null,
                        new Window(20107446, WindowSize::fromString('T1M'))
                    )
                ),
                $expected
            ];
        }

        return $data;
    }
}
