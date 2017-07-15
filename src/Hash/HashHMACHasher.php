<?php

namespace Krixon\MultiFactorAuth\Hash;

class HashHMACHasher implements Hasher
{
    public function hash(string $value, string $key, string $algorithm = Algorithm::SHA1) : string
    {
        Algorithm::assertSupported($algorithm);

        return hash_hmac($algorithm, $value, $key, true);
    }
}
