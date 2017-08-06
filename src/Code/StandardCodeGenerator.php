<?php

namespace Krixon\MultiFactorAuth\Code;

use Krixon\MultiFactorAuth\Clock\Clock;
use Krixon\MultiFactorAuth\Clock\SystemClock;
use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\Codec\Codec;
use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuth\OCRA\OCRASuite;

class StandardCodeGenerator implements CodeGenerator
{
    private $clock;
    private $algorithm;
    private $codec;


    /**
     * @param Clock|null  $clock     A clock. If none is provides, the system clock will be used.
     * @param Algorithm   $algorithm The hash algorithm used when generating time-based codes. This argument does not
     *                               apply to event-based or OCRA codes which always use SHA1 per RFC4226 (HOTP).
     * @param Codec|null  $codec     The codec to use for decoding the secret. If none is specified, this defaults
     *                               to base32 which is also the default codec used for secret generation. The
     *                               PassThroughCodec can be passed if secrets are not encoded at all.
     */
    public function __construct(Clock $clock = null, Algorithm $algorithm = null, Codec $codec = null)
    {
        $this->clock     = $clock     ?: new SystemClock();
        $this->algorithm = $algorithm ?: Algorithm::sha1();
        $this->codec     = $codec     ?: new Base32Codec();
    }


    public function generateTimeBasedCode(string $secret, int $time = null, int $codeLength = 6) : string
    {
        $window = $this->clock->window($time);
        $window = "\0\0\0\0" . pack('N*', $window);

        return $this->generateCode($secret, $window, $this->algorithm, $codeLength);
    }


    public function generateEventBasedCode(string $secret, int $counter, int $codeLength = 6) : string
    {
        $counter = "\0\0\0\0" . pack('N*', $counter);

        return $this->generateCode($secret, $counter, Algorithm::sha1(), $codeLength);
    }


    public function generateOCRACode(string $secret, OCRASuite $suite, int $codeLength = 6) : string
    {
        $message = $suite->toMessage();

        return $this->generateCode($secret, $message, $suite->cryptoFunction()->algorithm(), $codeLength);
    }


    public function algorithm() : Algorithm
    {
        return $this->algorithm;
    }


    public function clock() : Clock
    {
        return $this->clock;
    }


    private function generateCode(string $secret, string $bytes, Algorithm $algorithm, int $codeLength) : string
    {
        $secret = $this->codec->decode($secret);
        $hash   = hash_hmac($algorithm, $bytes, $secret, true);
        $offset = ord(substr($hash, -1)) & 0xF;

        $decimal = (
            ((ord($hash[$offset])     & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) <<  8) |
            ( ord($hash[$offset + 3]) & 0xFF)
        );

        return substr($decimal, -$codeLength);
    }
}
