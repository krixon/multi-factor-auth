<?php

namespace Krixon\MultiFactorAuthTests\Unit\Hash;

use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuth\Hash\Exception\UnsupportedAlgorithm;
use Krixon\MultiFactorAuthTests\TestCase;

class AlgorithmTest extends TestCase
{
    public function testStaticFactoriesConstructExpectedInstances() : void
    {
        static::assertSame(Algorithm::SHA1, (string)Algorithm::sha1());
        static::assertSame(Algorithm::SHA256, (string)Algorithm::sha256());
        static::assertSame(Algorithm::SHA512, (string)Algorithm::sha512());
    }


    public function testThrowsExpectedExceptionOnUnsupportedAlgorithm() : void
    {
        $this->expectException(UnsupportedAlgorithm::class);

        new Algorithm('NotAnAlgorithm');
    }
}
