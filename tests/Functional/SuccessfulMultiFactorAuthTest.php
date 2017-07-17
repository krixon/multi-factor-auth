<?php

namespace Krixon\MultiFactorAuthTests\Functional;

use Krixon\MultiFactorAuth\Code\Code;
use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\MultiFactorAuth;
use Krixon\MultiFactorAuthTests\TestCase;

class SuccessfulMultiFactorAuthTest extends TestCase
{
    public function testDefaultMultiFactorAuthConfiguration()
    {
        $this->runTests(MultiFactorAuth::default('Test Issuer'));
    }


    private function runTests(MultiFactorAuth $mfa, $secretBitCount = 160, $codeLength = 6)
    {
        // Can we generate a valid, 160bit secret?
        $secret    = $mfa->generateSecret();
        $rawSecret = (new Base32Codec())->decode($secret);

        static::assertInternalType('string', $secret);
        static::assertBitCountGreaterThanOrEqualTo($secretBitCount, $rawSecret);

        // Can we use the secret to generate valid time-based codes?
        $code       = $mfa->generateTimeBasedCode($secret);
        $codeString = $code->toString($codeLength);

        static::assertInstanceOf(Code::class, $code);
        static::assertRegExp('/\d{' . $codeLength . '}/', $codeString);
        static::assertTrue($mfa->verifyTimeBasedCode($secret, $codeString));

        // Can we use the secret to generate valid event-based codes?
        $counter    = 0;
        $code       = $mfa->generateEventBasedCode($secret, $counter);
        $codeString = $code->toString($codeLength);

        static::assertInstanceOf(Code::class, $code);
        static::assertRegExp('/\d{' . $codeLength . '}/', $codeString);
        static::assertTrue($mfa->verifyEventBasedCode($secret, $codeString, $counter));
    }
}
