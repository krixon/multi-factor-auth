<?php

namespace Krixon\MultiFactorAuth\Random;

class RandomBytes implements RandomNumberGenerator
{
    public function generateRandomBytes(int $byteCount) : string
    {
        try {
            return random_bytes($byteCount);
        } catch (\Exception $e) {
            throw new Exception\RandomNumberGenerationFailed($e);
        }
    }
}
