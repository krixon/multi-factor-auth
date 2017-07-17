<?php

namespace Krixon\MultiFactorAuthTests\Unit;

use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\Codec\Codec;
use Krixon\MultiFactorAuth\MultiFactorAuth;
use Krixon\MultiFactorAuthTests\TestCase;

class MultiFactorAuthTest extends TestCase
{
    /**
     * @var Codec
     */
    private static $codec;


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$codec = new Base32Codec();
    }


    /**
     * @dataProvider validCodesProvider
     *
     * @param array $expectedCodes
     * @param int   $initialCounter
     * @param int   $length
     */
    public function testGeneratesExpectedBackupCodes(array $expectedCodes, int $initialCounter = 0, int $length = 6)
    {
        $mfa      = MultiFactorAuth::default('Test Issuer');
        $secret   = static::$codec->encode('12345678901234567890');
        $numCodes = count($expectedCodes);

        $actualCodes = $mfa->generateBackupCodes($secret, $initialCounter, $numCodes);

        static::assertCount($numCodes, $actualCodes);

        foreach ($expectedCodes as $i => $expectedCode) {
            static::assertSame($expectedCode, $actualCodes[$i]->toString($length));
        }
    }


    public function validCodesProvider()
    {
        return [
            [['755224', '287082', '359152', '969429', '338314'], 0],
            [['254676', '287922', '162583', '399871', '520489'], 5],
        ];
    }


    /**
     * @dataProvider valueCodesForCountersProvider
     *
     * @param array $expectedCodes
     * @param int   $length
     */
    public function testGeneratesExpectedBackupCodesForCounters(array $expectedCodes, int $length = 6)
    {
        $mfa    = MultiFactorAuth::default('Test Issuer');
        $secret = static::$codec->encode('12345678901234567890');

        $actualCodes = $mfa->generateBackupCodesForCounters($secret, ...array_keys($expectedCodes));

        static::assertCount(count($expectedCodes), $actualCodes);

        foreach ($expectedCodes as $i => $expectedCode) {
            static::assertSame($expectedCode, $actualCodes[$i]->toString($length));
        }
    }


    public function valueCodesForCountersProvider()
    {
        return [
            [[
                0 => '755224', 1 => '287082', 2 => '359152', 3 => '969429', 4 => '338314',
                5 => '254676', 6 => '287922', 7 => '162583', 8 => '399871', 9 => '520489',
            ]],
            [[0 => '755224', 2 => '359152', 4 => '338314', 6 => '287922', 8 => '399871']],
            [[1 => '287082', 3 => '969429', 5 => '254676', 7 => '162583', 9 => '520489']],
            [[]],
            [[0 => '755224']],
            [[9 => '520489']],
            [[0 => '755224', 9 => '520489']],
        ];
    }
}
