<?php

namespace Krixon\MultiFactorAuth\Random;

interface RandomNumberGenerator
{
    public function generateRandomBytes(int $byteCount) : string;
}
