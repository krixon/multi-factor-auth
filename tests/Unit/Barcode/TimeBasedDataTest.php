<?php

namespace Krixon\MultiFactorAuthTests\Unit\Barcode;

use Krixon\MultiFactorAuth\Barcode\Data;
use Krixon\MultiFactorAuth\Barcode\Exception\InvalidData;
use Krixon\MultiFactorAuth\Barcode\TimeBasedData;
use Krixon\MultiFactorAuth\Hash\Algorithm;

class TimeBasedDataTest extends DataTest
{
    public function testWithWindowLength()
    {
        $windowLength = 42;
        $data         = $this->data();

        static::assertNotSame($windowLength, $data->windowLength());
        static::assertSame($windowLength, $data->withWindowLength($windowLength)->windowLength());
    }


    public function testWithAlgorithm()
    {
        $algorithm = Algorithm::sha256();
        $data      = $this->data();

        static::assertNotSame($algorithm, $data->algorithm());
        static::assertSame($algorithm, $data->withAlgorithm($algorithm)->algorithm());
    }


    public function testThrowsIfWindowLengthLessThanZero()
    {
        $this->expectException(InvalidData::class);

        $this->data()->withWindowLength(-1);
    }


    /**
     * @param string $secret
     * @param string $issuer
     * @param string $accountName
     * @param int    $digitCount
     *
     * @return Data|TimeBasedData
     */
    protected function data(
        string $secret = 'foo',
        string $issuer = 'Test Issuer',
        string $accountName = 'rimmer@jmc.org',
        int $digitCount = 6
    ) : Data {
        return new TimeBasedData($secret, $issuer, $accountName, $digitCount, 30, Algorithm::sha1());
    }
}
