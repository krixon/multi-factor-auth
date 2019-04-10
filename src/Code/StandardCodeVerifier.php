<?php

namespace Krixon\MultiFactorAuth\Code;

use Krixon\MultiFactorAuth\Clock\Clock;

class StandardCodeVerifier implements CodeVerifier
{
    private $maxWindowOffset;
    private $codeGenerator;


    /**
     * The $maxWindowOffset parameter provides a mechanism for protecting against clock skew issues. It defines the
     * number of windows either side of the current time which are included in the verification process. For
     * example, when it is set to 1, the previous and next windows will be checked against the code. With a
     * 30 second window length, a value of 1 would make codes valid for 60 seconds. Higher values reduce the risk of
     * clock skew issues but decrease security.
     *
     * @param CodeGenerator $codeGenerator
     * @param int           $maxWindowOffset
     */
    public function __construct(CodeGenerator $codeGenerator, int $maxWindowOffset = 1)
    {
        $this->codeGenerator   = $codeGenerator;
        $this->maxWindowOffset = $maxWindowOffset;
    }


    public function verifyEventBasedCode(string $secret, string $code, int $counter) : bool
    {
        $candidate = $this->codeGenerator->generateEventBasedCode($secret, $counter);

        return hash_equals($candidate, $code);
    }


    public function verifyTimeBasedCode(string $secret, string $code) : bool
    {
        $times = $this->clock()->times(null, $this->maxWindowOffset);

        foreach ($times as $time) {
            $candidate = $this->codeGenerator->generateTimeBasedCode($secret, $time);
            if (hash_equals($candidate, $code)) {
                return true;
            }
        }

        return false;
    }


    protected function clock() : Clock
    {
        return $this->codeGenerator->clock();
    }
}
