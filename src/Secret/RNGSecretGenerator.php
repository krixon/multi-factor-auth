<?php

namespace Krixon\MultiFactorAuth\Secret;

use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\Codec\Codec;
use Krixon\MultiFactorAuth\Random\RandomNumberGenerator;

class RNGSecretGenerator implements SecretGenerator
{
    private $randomNumberGenerator;
    private $codec;


    public function __construct(RandomNumberGenerator $randomNumberGenerator, Codec $codec = null)
    {
        $this->randomNumberGenerator = $randomNumberGenerator;
        $this->codec                 = $codec ?: new Base32Codec();
    }


    public function generateSecret(int $bitCount = 160) : string
    {
        $secret = $this->randomNumberGenerator->generateRandomBytes($bitCount / 8);

        return $this->codec->encode($secret);
    }
}
