<?php

namespace Krixon\MultiFactorAuth\Code;

interface CodeVerifier
{
    public function verifyEventBasedCode(string $code, string $secret, int $counter) : bool;
    public function verifyTimeBasedCode(string $code, string $secret) : bool;
}
