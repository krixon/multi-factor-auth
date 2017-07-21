<?php

namespace Krixon\MultiFactorAuth\Hash;

class HashHMACHasher implements Hasher
{
    public function hash(string $value, string $key, Algorithm $algorithm = null) : string
    {
        $algorithm = $algorithm ?: Algorithm::sha1();

        return hash_hmac($algorithm, $value, $key, true);
    }
}
