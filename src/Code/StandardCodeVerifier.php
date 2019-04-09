<?php

namespace Krixon\MultiFactorAuth\Code;

use Krixon\MultiFactorAuth\Clock\Clock;

class StandardCodeVerifier implements CodeVerifier
{
    private $maxWindowOffset;
    private $codeGenerator;
    private $minCodeLength;


    /**
     * The $maxWindowOffset parameter provides a mechanism for protecting against clock skew issues. It defines the
     * number of windows either side of the current time which are included in the verification process. For
     * example, when it is set to 1, the previous and next windows will be checked against the code. With a
     * 30 second window length, a value of 1 would make codes valid for 60 seconds. Higher values reduce the risk of
     * clock skew issues but decrease security.
     *
     * @param CodeGenerator $codeGenerator
     * @param int           $maxWindowOffset
     * @param int           $minCodeLength   Enforces a minimum length on any supplied codes. This avoids weak
     *                                       verification, for example by providing a 1-digit code which has a 1 in
     *                                       10 chance of being verified successfully.
     */
    public function __construct(CodeGenerator $codeGenerator, int $maxWindowOffset = 1, int $minCodeLength = 6)
    {
        $this->codeGenerator   = $codeGenerator;
        $this->maxWindowOffset = $maxWindowOffset;
        $this->minCodeLength   = $minCodeLength;
    }


    public function verifyEventBasedCode(string $secret, string $code, int $counter) : bool
    {
        $codeLength = strlen($code);

        if (!$this->isCodeLengthSatisfied($codeLength)) {
            return false;
        }

        $candidate = $this->codeGenerator->generateEventBasedCode($secret, $counter, $codeLength);

        return hash_equals($candidate, $code);
    }


    public function verifyTimeBasedCode(string $secret, string $code) : bool
    {
        $codeLength = strlen($code);

        if (!$this->isCodeLengthSatisfied($codeLength)) {
            return false;
        }

        $result = false;
        $times  = $this->clock()->times(null, $this->maxWindowOffset);

        foreach ($times as $time) {
            $candidate = $this->codeGenerator->generateTimeBasedCode($secret, $time, $codeLength);
            $result   |= hash_equals($candidate, $code);
        }

        return $result;
    }


    protected function clock() : Clock
    {
        return $this->codeGenerator->clock();
    }


    private function isCodeLengthSatisfied(int $codeLength) : bool
    {
        return $codeLength >= $this->minCodeLength;
    }
}
