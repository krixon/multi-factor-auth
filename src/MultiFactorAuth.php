<?php

namespace Krixon\MultiFactorAuth;

use Krixon\MultiFactorAuth\Barcode\Barcode;
use Krixon\MultiFactorAuth\Barcode\BarcodeGenerator;
use Krixon\MultiFactorAuth\Barcode\Data;
use Krixon\MultiFactorAuth\Barcode\EventBasedData;
use Krixon\MultiFactorAuth\Barcode\GoogleQRGenerator;
use Krixon\MultiFactorAuth\Barcode\Options;
use Krixon\MultiFactorAuth\Barcode\TimeBasedData;
use Krixon\MultiFactorAuth\Clock\Clock;
use Krixon\MultiFactorAuth\Code\Code;
use Krixon\MultiFactorAuth\Code\CodeGenerator;
use Krixon\MultiFactorAuth\Code\CodeVerifier;
use Krixon\MultiFactorAuth\Code\StandardCodeGenerator;
use Krixon\MultiFactorAuth\Code\StandardCodeVerifier;
use Krixon\MultiFactorAuth\HTTP\CurlClient;
use Krixon\MultiFactorAuth\Random\RandomBytes;
use Krixon\MultiFactorAuth\Secret\RNGSecretGenerator;
use Krixon\MultiFactorAuth\Secret\SecretGenerator;

class MultiFactorAuth implements CodeVerifier, CodeGenerator, SecretGenerator, BarcodeGenerator
{
    private $issuer;
    private $secretGenerator;
    private $codeGenerator;
    private $codeVerifier;
    private $barcodeGenerator;
    private $digitCount;


    public function __construct(
        string $issuer,
        SecretGenerator $secretGenerator,
        CodeGenerator $codeGenerator,
        CodeVerifier $eventBasedCodeVerifier,
        BarcodeGenerator $barcodeGenerator,
        int $digitCount = Code::DEFAULT_DIGIT_COUNT
    ) {
        $this->issuer           = $issuer;
        $this->secretGenerator  = $secretGenerator;
        $this->codeGenerator    = $codeGenerator;
        $this->codeVerifier     = $eventBasedCodeVerifier;
        $this->barcodeGenerator = $barcodeGenerator;
        $this->digitCount       = $digitCount;
    }


    public static function default(string $issuer)
    {
        $secretGenerator  = new RNGSecretGenerator(new RandomBytes());
        $codeGenerator    = new StandardCodeGenerator();
        $verifier         = new StandardCodeVerifier($codeGenerator);
        $barcodeGenerator = new GoogleQRGenerator(new CurlClient());

        return new static(
            $issuer,
            $secretGenerator,
            $codeGenerator,
            $verifier,
            $barcodeGenerator
        );
    }


    public function setSecretGenerator(SecretGenerator $secretGenerator) : void
    {
        $this->secretGenerator = $secretGenerator;
    }


    public function setCodeGenerator(CodeGenerator $codeGenerator) : void
    {
        $this->codeGenerator = $codeGenerator;
    }


    public function setCodeVerifier(CodeVerifier $codeVerifier) : void
    {
        $this->codeVerifier = $codeVerifier;
    }


    public function setBarcodeGenerator(BarcodeGenerator $barcodeGenerator) : void
    {
        $this->barcodeGenerator = $barcodeGenerator;
    }


    public function generateSecret(int $bitCount = 160) : string
    {
        return $this->secretGenerator->generateSecret($bitCount);
    }


    public function generateTimeBasedCode(string $secret, int $time = null) : Code
    {
        return $this->codeGenerator->generateTimeBasedCode($secret, $time);
    }


    public function generateEventBasedCode(string $secret, int $counter) : Code
    {
        return $this->codeGenerator->generateEventBasedCode($secret, $counter);
    }


    /**
     * Generates a specified number of backup codes.
     *
     * This is just a convenient way to generate multiple event-based codes at once.
     *
     * The generated codes (or the counter position and offset) can be stored server-side. The user can record the
     * codes, for example by writing them down on paper or entering them into a password manager. The server can
     * then check against any of these codes (in addition to the current code) during the verification process.
     *
     * @param string $secret
     * @param int    $counter
     * @param int    $numCodes
     *
     * @return Code[]
     */
    public function generateBackupCodes(string $secret, int $counter, int $numCodes = 10) : array
    {
        $codes = [];

        while ($numCodes--) {
            $codes[] = $this->generateEventBasedCode($secret, $counter++);
        }

        return $codes;
    }


    public function verifyEventBasedCode(string $code, string $secret, int $counter) : bool
    {
        return $this->codeVerifier->verifyEventBasedCode($code, $secret, $counter);
    }


    public function verifyTimeBasedCode(string $code, string $secret) : bool
    {
        return $this->codeVerifier->verifyTimeBasedCode($code, $secret);
    }


    public function generateBarCode(Data $data, Options $options = null) : Barcode
    {
        return $this->barcodeGenerator->generateBarcode($data, $options);
    }


    public function generateTimeBasedBarcode(string $secret, string $accountName, Options $options = null)
    {
        $data = new TimeBasedData(
            $secret,
            $this->issuer,
            $accountName,
            $this->digitCount,
            $this->clock()->windowLength(),
            $this->algorithm()
        );

        return $this->generateBarcode($data, $options);
    }


    public function generateEventBasedBarcode(
        string $secret,
        string $accountName,
        Options $options = null,
        $counter = 0
    ) {
        $data = new EventBasedData(
            $secret,
            $this->issuer,
            $accountName,
            $this->digitCount,
            $counter
        );

        return $this->generateBarcode($data, $options);
    }


    public function algorithm() : string
    {
        return $this->codeGenerator->algorithm();
    }


    public function clock() : Clock
    {
        return $this->codeGenerator->clock();
    }
}
