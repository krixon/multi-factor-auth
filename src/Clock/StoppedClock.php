<?php

namespace Krixon\MultiFactorAuth\Clock;

class StoppedClock extends BaseClock
{
    private $time;


    public function __construct(int $time, $windowLength = 30, $epoch = 0)
    {
        parent::__construct($windowLength, $epoch);

        $this->time = $time;
    }


    public function currentTime() : int
    {
        return $this->time;
    }
}
