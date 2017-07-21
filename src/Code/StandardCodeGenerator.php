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
     * @param Algorithm   $algorithm The hash algorithm used when generating time-based codes. This argument does not
     *                               apply to event-based codes which always use SHA1 per RFC4226 (HOTP).
     * @param Codec|null  $codec     The codec to use for decoding the secret. If none is specified, this defaults
     *                               to base32 which is also the default codec used for secret generation. The
     *                               PassThroughCodec can be passed if secrets are not encoded at all.
     */
    public function __construct(
        Clock $clock = null,
        Hasher $hasher = null,
        Algorithm $algorithm = null,
        Codec $codec = null
    ) {
        $this->clock     = $clock     ?: new SystemClock();
        $this->hasher    = $hasher    ?: new HashHMACHasher();
        $this->algorithm = $algorithm ?: Algorithm::SHA1();
        $this->codec     = $codec     ?: new Base32Codec();
    }


    public function generateTimeBasedCode(string $secret, int $time = null) : Code
    {
        $window = $this->clock->window($time);

        return $this->generateCode($secret, $window, $this->algorithm);
    }


    public function generateEventBasedCode(string $secret, int $counter) : Code
    {
        return $this->generateCode($secret, $counter, Algorithm::SHA1());
    }


    public function algorithm() : Algorithm
    {
        return $this->algorithm;
    }


    public function clock() : Clock
    {
        return $this->clock;
    }


    private function generateCode(string $secret, int $factor, Algorithm $algorithm) : Code
    {
        $secret = $this->codec->decode($secret);
        $bytes  = "\0\0\0\0" . pack('N*', $factor);
        $hash   = $this->hasher->hash($bytes, $secret, $algorithm);
        $offset = ord(substr($hash, -1)) & 0xF;

        $decimal = (
            ((ord($hash[$offset])     & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) <<  8) |
            ( ord($hash[$offset + 3]) & 0xFF)
        );

        return new Code($decimal);
    }
}
