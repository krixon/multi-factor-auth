<?php

namespace Krixon\MultiFactorAuth\Hash;

final class Algorithm
{
    public const SHA1   = 'SHA1';
    public const SHA256 = 'SHA256';
    public const SHA512 = 'SHA512';

    private const ENUM = [
        self::SHA1,
        self::SHA256,
        self::SHA512,
    ];

    private $algorithm;


    public function __construct(string $algorithm)
    {
        $algorithm = strtoupper($algorithm);

        self::assertSupported($algorithm);

        $this->algorithm = $algorithm;
    }


    public static function sha1() : self
    {
        return new self(self::SHA1);
    }


    public static function sha256() : self
    {
        return new self(self::SHA256);
    }


    public static function sha512() : self
    {
        return new self(self::SHA512);
    }


    public function __toString()
    {
        return $this->algorithm;
    }


    private static function assertSupported(string $algorithm) : void
    {
        if (!in_array($algorithm, self::ENUM, true)) {
            throw new Exception\UnsupportedAlgorithm($algorithm);
        }
    }
}
