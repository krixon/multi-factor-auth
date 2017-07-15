<?php

namespace Krixon\MultiFactorAuthTests\Unit\Clock;

use Krixon\MultiFactorAuth\Clock\BaseClock;
use Krixon\MultiFactorAuthTests\TestCase;

class WindowCalculatingClockTest extends TestCase
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
        $clock = $this->clock($time, $windowLength, $epoch);
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


    private function clock(int $currentTime, int $windowLength, int $epoch) : BaseClock
    {
        return new class($currentTime, $windowLength, $epoch) extends BaseClock
        {
            private $time;


            public function __construct(int $time, $windowLength, $epoch)
            {
                parent::__construct($windowLength, $epoch);

                $this->time = $time;
            }


            public function currentTime() : int
            {
                return $this->time;
            }
        };
    }
}
