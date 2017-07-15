<?php

namespace Krixon\MultiFactorAuth\Code;

use Krixon\MultiFactorAuth\Clock\Clock;
use Krixon\MultiFactorAuth\Clock\SystemClock;
use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\Codec\Codec;
use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuth\Hash\Hasher;
use Krixon\MultiFactorAuth\Hash\HashHMACHasher;

class StandardCodeGenerator implements CodeGenerator
{
    private $clock;
    private $hasher;
    private $algorithm;
    private $codec;


    /**
     * @param Clock|null  $clock     A clock. If none is provides, the system clock will be used.
     * @param Hasher|null $hasher    The HMAC hasher to use. If none is provided, an implementation based on the
     *                               hash_hmac() function will be used.
     * @param string      $algorithm The hash algorithm used when generating time-based codes. Event-based codes
     *                               always use SHA1 per RFC4226 (HOTP).
     * @param Codec|null  $codec
     */
    public function __construct(
        Clock $clock = null,
        Hasher $hasher = null,
        string $algorithm = Algorithm::SHA1,
        Codec $codec = null
    ) {
        $this->clock     = $clock ?: new SystemClock();
        $this->hasher    = $hasher ?: new HashHMACHasher();
        $this->algorithm = $algorithm;
        $this->codec     = $codec ?: new Base32Codec();
    }


    public function generateTimeBasedCode(string $secret, int $time = null) : Code
    {
        $window = $this->clock->window($time);

        return $this->generateCode($secret, $window, $this->algorithm);
    }


    public function generateEventBasedCode(string $secret, int $counter) : Code
    {
        return $this->generateCode($secret, $counter, Algorithm::SHA1);
    }


    public function algorithm() : string
    {
        return $this->algorithm;
    }


    public function clock() : Clock
    {
        return $this->clock;
    }


    private function generateCode(string $secret, int $factor, string $algorithm) : Code
    {
        $secret = $this->codec->decode($secret);
        $bytes  = "\0\0\0\0" . pack('N*', $factor);
        $hash   = $this->hasher->hash($bytes, $secret, $algorithm);

        return new Code($hash);
    }
}
