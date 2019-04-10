<?php

namespace Krixon\MultiFactorAuthTests;

use Krixon\MultiFactorAuth\Barcode\BarcodeGenerator;
use Krixon\MultiFactorAuth\Barcode\Data;
use Krixon\MultiFactorAuth\Barcode\EventBasedData;
use Krixon\MultiFactorAuth\Barcode\Options;
use Krixon\MultiFactorAuth\Barcode\TimeBasedData;
use Krixon\MultiFactorAuth\Code\CodeGenerator;
use Krixon\MultiFactorAuth\Code\CodeVerifier;
use Krixon\MultiFactorAuth\MultiFactorAuth;
use Krixon\MultiFactorAuth\Secret\SecretGenerator;
use ReflectionException;

class MultiFactorAuthTest extends TestCase
{
    /**
     * @var MultiFactorAuth
     */
    private $mfa;


    protected function setUp() : void
    {
        parent::setUp();

        $this->mfa = MultiFactorAuth::default('Test Issuer');
    }


    /**
     * @throws ReflectionException
     */
    public function testUsesSetSecretGenerator() : void
    {
        $generator = $this->createMock(SecretGenerator::class);

        $generator->expects($this->once())->method('generateSecret');

        /** @noinspection PhpParamsInspection */
        $this->mfa->setSecretGenerator($generator);

        $this->mfa->generateSecret();
    }


    /**
     * @throws ReflectionException
     */
    public function testUsesSetBarcodeGenerator() : void
    {
        $data      = $this->createMock(Data::class);
        $generator = $this->createMock(BarcodeGenerator::class);

        $generator->expects($this->once())->method('generateBarcode')->with($data);

        /** @noinspection PhpParamsInspection */
        $this->mfa->setBarcodeGenerator($generator);

        /** @noinspection PhpParamsInspection */
        $this->mfa->generateBarCode($data);
    }


    /**
     * @throws ReflectionException
     */
    public function testUsesSetCodeGenerator() : void
    {
        $secret    = 'secret';
        $generator = $this->createMock(CodeGenerator::class);

        $generator->expects($this->once())->method('generateTimeBasedCode')->with($secret);

        /** @noinspection PhpParamsInspection */
        $this->mfa->setCodeGenerator($generator);

        $this->mfa->generateTimeBasedCode($secret);
    }


    /**
     * @throws ReflectionException
     */
    public function testUsesSetCodeVerifier() : void
    {
        $secret    = 'secret';
        $code      = 'code';
        $generator = $this->createMock(CodeVerifier::class);

        $generator->expects($this->once())->method('verifyTimeBasedCode')->with($secret, $code);

        /** @noinspection PhpParamsInspection */
        $this->mfa->setCodeVerifier($generator);

        $this->mfa->verifyTimeBasedCode($secret, $code);
    }


    /**
     * @throws ReflectionException
     */
    public function testPassesCorrectDataToBarcodeGenerator() : void
    {
        $secret      = 'secret';
        $accountName = 'Account Name';
        $issuer      = 'Test Issuer';
        $generator   = $this->createMock(BarcodeGenerator::class);

        $generator
            ->expects($this->exactly(2))
            ->method('generateBarcode')
            ->withConsecutive(
                [
                    $this->callback(static function ($subject) use ($issuer) {
                        return $subject instanceof TimeBasedData && $subject->issuer() === $issuer;
                    }),
                    $this->isNull(),
                ],
                [
                    $this->callback(static function ($subject) use ($issuer) {
                        return $subject instanceof EventBasedData && $subject->issuer() === $issuer;
                    }),
                    $this->isNull(),
                ]
            );

        /** @noinspection PhpParamsInspection */
        $this->mfa->setBarcodeGenerator($generator);

        $this->mfa->generateTimeBasedBarcode($secret, $accountName);
        $this->mfa->generateEventBasedBarcode($secret, $accountName);
    }


    /**
     * @throws ReflectionException
     */
    public function testPassesOptionsThroughToBarcodeGenerator() : void
    {
        $options   = Options::default();
        $generator = $this->createMock(BarcodeGenerator::class);

        $generator
            ->expects($this->exactly(2))
            ->method('generateBarcode')
            ->withConsecutive(
                [
                    $this->isInstanceOf(TimeBasedData::class),
                    $options,
                ],
                [
                    $this->isInstanceOf(EventBasedData::class),
                    $options,
                ]
            );

        /** @noinspection PhpParamsInspection */
        $this->mfa->setBarcodeGenerator($generator);

        $this->mfa->generateTimeBasedBarcode('secret', 'Account Name', $options);
        $this->mfa->generateEventBasedBarcode('secret', 'Account Name', $options);
    }
}
