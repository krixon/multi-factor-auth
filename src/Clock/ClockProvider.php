<?php

namespace Krixon\MultiFactorAuth\Clock;

interface ClockProvider
{
    public function clock() : Clock;
}
