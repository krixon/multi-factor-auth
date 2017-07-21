<?php

namespace Krixon\MultiFactorAuth\Hash;

interface Hasher
{
    /**
     * Generates HMACs using a specified algorithm.
     *
     * This returns raw binary strings.
     *
     * @param string         $value     The value to hash.
     * @param string         $key       Shared key to use for generating the HMAC variant of the hash.
     * @param Algorithm|null $algorithm The hashing algorithm to use. If none is specified, this defaults to SHA1.
     *
     * @return string
     */
    public function hash(string $value, string $key, Algorithm $algorithm = null) : string;
}
