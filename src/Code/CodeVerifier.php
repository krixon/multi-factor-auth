<?php

namespace Krixon\MultiFactorAuth\Code;

interface CodeVerifier
{
    public function verifyEventBasedCode(string $secret, string $code, int $counter) : bool;
    public function verifyTimeBasedCode(string $secret, string $code) : bool;
}
