<?php

namespace Krixon\MultiFactorAuth\Clock;

abstract class BaseClock implements Clock
{
    private $windowLength;
    private $epoch;


    public function __construct(int $windowLength = 30, int $epoch = 0)
    {
        if ($windowLength <= 0) {
            throw new Exception\InvalidWindowLength($windowLength);
        }

        $this->windowLength = $windowLength;
        $this->epoch        = $epoch;
    }


    public function window(int $time = null) : int
    {
        $time = $this->resolveTime($time);

        return floor(($time - $this->epoch()) / $this->windowLength());
    }


    public function windowLength() : int
    {
        return $this->windowLength;
    }


    public function times(int $time = null, int $offset = 1) : array
    {
        $time  = $this->resolveTime($time);
        $times = [];

        for ($i = -$offset; $i <= $offset; $i++) {
            $times[$i] = $time + ($i * $this->windowLength);
        }

        return $times;
    }


    protected function epoch() : int
    {
        return $this->epoch;
    }


    private function resolveTime(?int $time)
    {
        if (null === $time) {
            $time = $this->currentTime();
        }

        return $time;
    }
}
