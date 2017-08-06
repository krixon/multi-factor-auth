<?php

namespace Krixon\MultiFactorAuth\Code;

use Krixon\MultiFactorAuth\Clock\ClockProvider;
use Krixon\MultiFactorAuth\Hash\AlgorithmProvider;
use Krixon\MultiFactorAuth\OCRA\OCRASuite;

interface CodeGenerator extends AlgorithmProvider, ClockProvider
{
    /**
     * Generates event-based codes which conform to RFC4226 (HOTP).
     *
     * An incrementing counter is used in order to generate codes.
     *
     * @param string $secret     The shared secret.
     * @param int    $counter    The current value of the counter.
     * @param int    $codeLength
     *
     * @return string
     */
    public function generateEventBasedCode(string $secret, int $counter, int $codeLength = 6) : string;


    /**
     * Generates time-based codes which conform to RFC6238 (TOTP).
     *
     * TOTPs are very similar to HOTPs (event-based codes). Here a time factor is used as the counter.
     *
     * Unlike event-based codes which are restricted to SHA-1 by the spec, time-based codes can optionally use SHA-256
     * or SHA-512 to hash the secret.
     *
     * @param string   $secret     The shared secret.
     * @param int|null $time       The time for which to generate a code. If not supplied the current time will be used.
     * @param int      $codeLength
     *
     * @return string
     */
    public function generateTimeBasedCode(string $secret, int $time = null, int $codeLength = 6) : string;


    /**
     * Generates challenge-response codes which conform to RFC6287 (OCRA: OATH Challenge-Response Algorithm).
     *
     * @param string    $secret
     * @param OCRASuite $suite
     *
     * @return string
     */
    public function generateOCRACode(string $secret, OCRASuite $suite) : string;
}
