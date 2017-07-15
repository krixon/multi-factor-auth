<?php

namespace Krixon\MultiFactorAuth\Hash;

final class Algorithm
{
    public const SHA1   = 'sha1';
    public const SHA256 = 'sha256';
    public const SHA512 = 'sha512';

    private const ENUM = [
        self::SHA1,
        self::SHA256,
        self::SHA512,
    ];


    public static function assertSupported(string $algorithm) : void
    {
        if (!in_array($algorithm, self::ENUM, true)) {
            throw new Exception\UnsupportedAlgorithm($algorithm);
        }
    }
}
