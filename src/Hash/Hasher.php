<?php

namespace Krixon\MultiFactorAuth\Hash;

/**
 * Generates HMACs using a specified algorithm.
 */
interface Hasher
{
    public function hash(string $value, string $key, string $algorithm = Algorithm::SHA1) : string;
}
