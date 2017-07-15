<?php

namespace Krixon\MultiFactorAuth\Clock;

class SystemClock extends BaseClock
{
    public function currentTime() : int
    {
        return time();
    }
}
