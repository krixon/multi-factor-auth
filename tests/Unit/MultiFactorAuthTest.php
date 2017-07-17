<?php

namespace Krixon\MultiFactorAuthTests\Unit;

use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\Codec\Codec;
use Krixon\MultiFactorAuth\Exception\CounterSynchronizationFailed;
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
    public function testGeneratesExpectedEventBasedCodes(array $expectedCodes, int $initialCounter = 0, int $length = 6)
    {
        $mfa      = MultiFactorAuth::default('Test Issuer');
        $secret   = static::$codec->encode('12345678901234567890');
        $numCodes = count($expectedCodes);

        $actualCodes = $mfa->generateEventBasedCodes($secret, $initialCounter, $numCodes);

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
     * @dataProvider synchronizesCounterProvider
     *
     * @param string $code
     * @param int    $counter
     * @param int    $lookahead
     * @param int    $expected
     */
    public function testSynchronizesCounter(string $code, int $counter, int $lookahead, int $expected)
    {
        $mfa    = MultiFactorAuth::default('Test Issuer');
        $secret = static::$codec->encode('12345678901234567890');

        $result = $mfa->synchronizeCounter($secret, $code, $counter, $lookahead);

        static::assertSame($expected, $result);
    }


    public function synchronizesCounterProvider()
    {
        return [
            // CODE  | COUNTER | LOOKAHEAD | EXPECTED

            // Counter is not out of sync.
            ['755224', 0,        10,         0],
            ['969429', 3,        10,         3],
            ['755224', 0,         0,         0], // 0 lookahead still works since the counter is in sync.

            // Counter is out of sync.
            // Off by one.
            ['287082', 0,        10,         1],
            ['359152', 1,        10,         2],
            ['969429', 2,        10,         3],
            ['338314', 3,        10,         4],
            ['254676', 4,        10,         5],
            ['254676', 4,         1,         5],
            // Off by more than one.
            ['287922', 0,        10,         6],
            ['162583', 0,        10,         7],
            ['399871', 0,        10,         8],
            ['520489', 0,        10,         9],
            ['520489', 0,         9,         9],
        ];
    }


    /**
     * @dataProvider cannotSynchronizeCounterWhenLookaheadTooSmallProvider
     *
     * @param string $code
     * @param int    $counter
     * @param int    $lookahead
     */
    public function testCannotSynchronizeCounterWhenLookaheadTooSmall(string $code, int $counter, int $lookahead)
    {
        static::expectException(CounterSynchronizationFailed::class);

        $mfa    = MultiFactorAuth::default('Test Issuer');
        $secret = static::$codec->encode('12345678901234567890');

        $mfa->synchronizeCounter($secret, $code, $counter, $lookahead);
    }


    public function cannotSynchronizeCounterWhenLookaheadTooSmallProvider()
    {
        return [
            // CODE  | COUNTER | LOOKAHEAD
            ['287082', 0,         0],
            ['359152', 1,         0],
            ['969429', 2,         0],
            ['338314', 3,         0],
            ['254676', 4,         0],
            ['254676', 0,         4],
            ['520489', 0,         8],
        ];
    }
}
