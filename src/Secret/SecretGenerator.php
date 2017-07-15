<?php

namespace Krixon\MultiFactorAuth\Secret;

interface SecretGenerator
{
    public function generateSecret(int $bitCount = 160) : string;
}
