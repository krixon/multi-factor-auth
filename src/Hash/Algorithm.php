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


    public static function fromString(string $string) : self
    {
        switch ($string) {
            case self::SHA1:
                return static::sha1();
            case self::SHA256:
                return static::sha256();
            case self::SHA512:
                return static::sha512();
            default:
                throw new Exception\UnsupportedAlgorithm($string);
        }
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


    public function is(string $algorithm) : bool
    {
        return $this->algorithm === strtoupper($algorithm);
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
