<?php

namespace Krixon\MultiFactorAuthTests\Unit\Hash;

use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuth\Hash\Exception\UnsupportedAlgorithm;
use Krixon\MultiFactorAuthTests\TestCase;

class AlgorithmTest extends TestCase
{
    public function testStaticFactoriesConstructExpectedInstances()
    {
        static::assertSame(Algorithm::SHA1, (string)Algorithm::SHA1());
        static::assertSame(Algorithm::SHA256, (string)Algorithm::SHA256());
        static::assertSame(Algorithm::SHA512, (string)Algorithm::SHA512());
    }


    public function testThrowsExpectedExceptionOnUnsupportedAlgorithm()
    {
        $this->expectException(UnsupportedAlgorithm::class);

        new Algorithm('NotAnAlgorithm');
    }
}
