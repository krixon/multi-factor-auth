<?php

namespace Krixon\MultiFactorAuthTests\Unit\Barcode;

use Krixon\MultiFactorAuth\Barcode\Data;
use Krixon\MultiFactorAuth\Barcode\EventBasedData;
use Krixon\MultiFactorAuth\Barcode\Exception\InvalidData;
use Krixon\MultiFactorAuth\Barcode\GeneratesKeyURIsFromData;
use Krixon\MultiFactorAuth\Barcode\TimeBasedData;
use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuthTests\TestCase;
use ReflectionException;

class GeneratesKeyUrisFromDataTest extends TestCase
{
    /**
     * @dataProvider expectedUriProvider
     *
     * @param Data   $data
     * @param string $expected
     */
    public function testGeneratesExpectedKeyUri(Data $data, string $expected) : void
    {
        $uri = $this->impl()->generateKeyURI($data);

        static::assertSame($expected, $uri);
    }


    public function expectedUriProvider() : array
    {
        $timeData  = new TimeBasedData('1234567890', 'Test Issuer', 'dave.lister@example.com');
        $eventData = new EventBasedData('1234567890', 'Test Issuer', 'dave.lister@example.com');

        /** @noinspection SpellCheckingInspection */
        return [
            // Account name variations.
            [
                $timeData->withAccountName('Account name can be long and contain whitespace and punctuation.'),
                'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253AAccount%2520name%2520can%2520be%2520long%2520' .
                'and%2520contain%2520whitespace%2520and%2520punctuation.%3Fsecret%3D1234567890%26issuer%3D' .
                'Test%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1'
            ],
            [
                $eventData->withAccountName('Account name can be long and contain whitespace and punctuation.'),
                'otpauth%3A%2F%2Fhotp%2FTest%2520Issuer%253AAccount%2520name%2520can%2520be%2520long%2520' .
                'and%2520contain%2520whitespace%2520and%2520punctuation.%3Fsecret%3D1234567890%26issuer%3D' .
                'Test%2520Issuer%26digits%3D6%26counter%3D0%26algorithm%3DSHA1'
            ],
            [
                $timeData->withAccountName('Url unsafe % characters ? are encoded = correctly'),
                'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253AUrl%2520unsafe%2520%2525%2520characters' .
                '%2520%253F%2520are%2520encoded%2520%253D%2520correctly%3Fsecret%3D1234567890%26issuer' .
                '%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1'
            ],
            // Secret variations.
            [
                $timeData->withSecret('Secret can be quite long and also contain whitespace and punctuation.'),
                'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3Fsecret%3D' .
                'Secret%2520can%2520be%2520quite%2520long%2520and%2520also%2520contain%2520whitespace' .
                '%2520and%2520punctuation.%26issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26' .
                'algorithm%3DSHA1'
            ],
            [
                $timeData->withSecret('Url unsafe % characters ? are : encoded = correctly'),
                'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3Fsecret%3DUrl%2520' .
                'unsafe%2520%2525%2520characters%2520%253F%2520are%2520%253A%2520encoded%2520%253D%2520' .
                'correctly%26issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1'
            ],
            // Algorithm variations.
            [
                $timeData->withAlgorithm(Algorithm::sha1()),
                'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3F' .
                'secret%3D1234567890%26issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA1'
            ],
            [
                $timeData->withAlgorithm(Algorithm::sha256()),
                'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3F' .
                'secret%3D1234567890%26issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA256'
            ],
            [
                $timeData->withAlgorithm(Algorithm::sha512()),
                'otpauth%3A%2F%2Ftotp%2FTest%2520Issuer%253Adave.lister%2540example.com%3F' .
                'secret%3D1234567890%26issuer%3DTest%2520Issuer%26digits%3D6%26period%3D30%26algorithm%3DSHA512'
            ],
        ];
    }


    /**
     * @throws ReflectionException
     */
    public function testThrowsOnUnknownDataType() : void
    {
        $this->expectException(InvalidData::class);

        $unknown = $this->createMock(Data::class);

        /** @noinspection PhpParamsInspection */
        $this->impl()->generateKeyURI($unknown);
    }


    private function impl()
    {
        return new class()
        {
            use GeneratesKeyURIsFromData {
                generateKeyURI as public;
            }
        };
    }
}
