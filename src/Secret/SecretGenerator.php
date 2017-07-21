<?php

namespace Krixon\MultiFactorAuth\Secret;

interface SecretGenerator
{
    public function generateSecret(int $byteCount = 160) : string;
}
