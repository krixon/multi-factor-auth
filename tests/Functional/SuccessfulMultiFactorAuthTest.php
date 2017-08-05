<?php

namespace Krixon\MultiFactorAuthTests\Functional;

use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\MultiFactorAuth;
use Krixon\MultiFactorAuthTests\TestCase;

class SuccessfulMultiFactorAuthTest extends TestCase
{
    public function testDefaultMultiFactorAuthConfiguration()
    {
        $this->runTests(MultiFactorAuth::default('Test Issuer'));
    }


    private function runTests(MultiFactorAuth $mfa, $secretByteCount = 20, $codeLength = 6)
    {
        // Can we generate a valid, 20B (160b) secret?
        $secret    = $mfa->generateSecret();
        $rawSecret = (new Base32Codec())->decode($secret);

        static::assertInternalType('string', $secret);
        static::assertByteCountGreaterThanOrEqualTo($secretByteCount, $rawSecret);

        // Can we use the secret to generate valid time-based codes?
        $code = $mfa->generateTimeBasedCode($secret, null, $codeLength);

        static::assertInternalType('string', $code);
        static::assertRegExp('/\d{' . $codeLength . '}/', $code);
        static::assertTrue($mfa->verifyTimeBasedCode($secret, $code));

        // Can we use the secret to generate valid event-based codes?
        $counter = 0;
        $code    = $mfa->generateEventBasedCode($secret, $counter, $codeLength);

        static::assertInternalType('string', $code);
        static::assertRegExp('/\d{' . $codeLength . '}/', $code);
        static::assertTrue($mfa->verifyEventBasedCode($secret, $code, $counter));
    }
}
