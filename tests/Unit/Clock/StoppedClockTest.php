<?php

namespace Krixon\MultiFactorAuthTests\Unit\Clock;

use Krixon\MultiFactorAuth\Clock\Exception\InvalidWindowLength;
use Krixon\MultiFactorAuth\Clock\StoppedClock;
use Krixon\MultiFactorAuthTests\TestCase;

class StoppedClockTest extends TestCase
{
    /**
     * @dataProvider correctTimesProvider
     *
     * @param int   $time
     * @param int   $offset
     * @param int   $windowLength
     * @param int   $epoch
     * @param int[] $expectedTimes
     */
    public function testCalculatesCorrectTimes(
        int $time,
        int $offset,
        int $windowLength,
        int $epoch,
        array $expectedTimes
    ) {
        $clock = new StoppedClock($time, $windowLength, $epoch);
        $times = $clock->times($time, $offset);

        sort($times);
        sort($expectedTimes);

        static::assertSame($expectedTimes, $times);
    }


    public function correctTimesProvider()
    {
        return [
            [0,  0, 30, 0, [0]],
            [15, 1, 30, 0, [-15, 15, 45]],
        ];
    }


    public function testCannotBeConstructedWithWindowLengthLessThanZero()
    {
        $this->expectException(InvalidWindowLength::class);

        new StoppedClock(1, -10, 0);
    }
}
