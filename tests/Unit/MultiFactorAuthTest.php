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

}
