<?php

namespace Krixon\MultiFactorAuthTests\Unit\Barcode;

use Krixon\MultiFactorAuth\Barcode\Data;
use Krixon\MultiFactorAuth\Barcode\Exception\InvalidData;
use Krixon\MultiFactorAuthTests\TestCase;

abstract class DataTest extends TestCase
{
    public function testWithSecret() : void
    {
        $secret = 'bar';
        $data   = $this->data();

        static::assertNotSame($secret, $data->secret());
        static::assertSame($secret, $data->withSecret($secret)->secret());
    }


    public function testWithIssuer() : void
    {
        $issuer = 'Different Issuer';
        $data   = $this->data();

        static::assertNotSame($issuer, $data->issuer());
        static::assertSame($issuer, $data->withIssuer($issuer)->issuer());
    }


    public function testThrowsIfIssuerContainsColon() : void
    {
        $this->expectException(InvalidData::class);

        $this->data()->withIssuer('foo:bar');
    }


    public function testThrowsIfAccountNameContainsColon() : void
    {
        $this->expectException(InvalidData::class);

        $this->data()->withAccountName('foo:bar');
    }


    public function testWithAccountName() : void
    {
        $accountName = 'Different Account Name';
        $data        = $this->data();

        static::assertNotSame($accountName, $data->accountName());
        static::assertSame($accountName, $data->withAccountName($accountName)->accountName());
    }


    public function testWithDigitCount() : void
    {
        $digitCount = 8;
        $data       = $this->data();

        static::assertNotSame($digitCount, $data->codeLength());
        static::assertSame($digitCount, $data->withCodeLength($digitCount)->codeLength());
    }


    abstract protected function data(
        string $secret = 'foo',
        string $issuer = 'Test Issuer',
        string $accountName = 'rimmer@jmc.org',
        int $digitCount = 6
    ) : Data;
}
