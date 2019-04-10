<?php

namespace Krixon\MultiFactorAuthTests\Unit\Barcode;

use Krixon\MultiFactorAuth\Barcode\Data;
use Krixon\MultiFactorAuth\Barcode\EventBasedData;
use Krixon\MultiFactorAuth\Barcode\Exception\InvalidData;

class EventBasedDataTest extends DataTest
{
    public function testWithCounter() : void
    {
        $counter = 42;
        $data    = $this->data();

        static::assertNotSame($counter, $data->counter());
        static::assertSame($counter, $data->withCounter($counter)->counter());
    }


    public function testThrowsIfCounterLessThanZero() : void
    {
        $this->expectException(InvalidData::class);

        $this->data()->withCounter(-1);
    }


    /**
     * @param string $secret
     * @param string $issuer
     * @param string $accountName
     * @param int    $digitCount
     *
     * @return Data|EventBasedData
     */
    protected function data(
        string $secret = 'foo',
        string $issuer = 'Test Issuer',
        string $accountName = 'rimmer@jmc.org',
        int $digitCount = 6
    ) : Data {
        return new EventBasedData($secret, $issuer, $accountName, $digitCount, 0);
    }
}
