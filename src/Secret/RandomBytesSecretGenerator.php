<?php

namespace Krixon\MultiFactorAuth\Secret;

use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\Codec\Codec;

class RandomBytesSecretGenerator implements SecretGenerator
{
    private $codec;


    public function __construct(Codec $codec = null)
    {
        $this->codec = $codec ?: new Base32Codec();
    }


    public function generateSecret(int $byteCount = 20) : string
    {
        try {
            $secret = random_bytes($byteCount);
        } catch (\Exception $e) {
            throw new Exception\SecretGenerationFailed($e);
        }

        return $this->codec->encode($secret);
    }
}
