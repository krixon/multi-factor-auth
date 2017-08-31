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
use Krixon\MultiFactorAuth\Code\CodeGenerator;
use Krixon\MultiFactorAuth\Code\CodeVerifier;
use Krixon\MultiFactorAuth\Code\StandardCodeGenerator;
use Krixon\MultiFactorAuth\Code\StandardCodeVerifier;
use Krixon\MultiFactorAuth\Hash\Algorithm;
use Krixon\MultiFactorAuth\HTTP\CurlClient;
use Krixon\MultiFactorAuth\Secret\RandomBytesSecretGenerator;
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
        CodeVerifier $codeVerifier,
        BarcodeGenerator $barcodeGenerator,
        int $digitCount = 6
    ) {
        $this->issuer           = $issuer;
        $this->secretGenerator  = $secretGenerator;
        $this->codeGenerator    = $codeGenerator;
        $this->codeVerifier     = $codeVerifier;
        $this->barcodeGenerator = $barcodeGenerator;
        $this->digitCount       = $digitCount;
    }


    public static function default(string $issuer)
    {
        $secretGenerator  = new RandomBytesSecretGenerator();
        $codeGenerator    = new StandardCodeGenerator();
        $verifier         = new StandardCodeVerifier($codeGenerator);
        $barcodeGenerator = new GoogleQRGenerator(new CurlClient());

        return new static($issuer, $secretGenerator, $codeGenerator, $verifier, $barcodeGenerator);
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


    public function generateSecret(int $byteCount = 20) : string
    {
        return $this->secretGenerator->generateSecret($byteCount);
    }


    public function generateTimeBasedCode(string $secret, int $time = null, int $codeLength = 6) : string
    {
        return $this->codeGenerator->generateTimeBasedCode($secret, $time, $codeLength);
    }


    public function generateEventBasedCode(string $secret, int $counter, int $codeLength = 6) : string
    {
        return $this->codeGenerator->generateEventBasedCode($secret, $counter, $codeLength);
    }


    /**
     * Generates a specified number of event-based codes.
     *
     * This is just a convenient way to generate multiple event-based codes at once.
     *
     * This is useful as part of a "backup codes" system. The generated codes can be stored server-side (salted
     * and hashed). The user can record the codes, for example by writing them down on paper or entering them into a
     * password manager. The server can then check against any of these codes (in addition to the current code)
     * during the verification process.
     *
     * @param string $secret
     * @param int    $counter
     * @param int    $numCodes
     * @param int    $codeLength
     *
     * @return string[]
     */
    public function generateEventBasedCodes(
        string $secret,
        int $counter,
        int $numCodes = 10,
        int $codeLength = 6
    ) : array {
        $codes = [];

        while ($numCodes--) {
            $codes[] = $this->generateEventBasedCode($secret, $counter++, $codeLength);
        }

        return $codes;
    }


    /**
     * Synchronizes the counter value based on a supplied code.
     *
     * Because codes are only incremented on the server after successful verification but can be incremented on the
     * client without ever being verified by the server, it is possible for the client's counter to get ahead of
     * the server's.
     *
     * This method provides a mechanism for detecting and resolving this situation. Given the latest code from the
     * client and the server's current counter, it will attempt to verify the current code plus a number of future
     * codes. If any is verified successfully, the corresponding counter value is returned. The server can then store
     * this updated counter to resynchronize with the client.
     *
     * For example, if the server's counter is 20 and the supplied code corresponds to a counter value of 23,
     * this method will return 23.
     *
     * The lookahead parameter defines the number of codes in addition to the current code which will be checked.
     * With a lookahead of 10, this means 11 codes in total will be checked (the current one plus the following 10).
     * A lower lookahead value is more secure but is less likely to result in successful resynchronization.
     *
     * @param string $secret
     * @param string $code
     * @param int    $counter
     * @param int    $lookahead
     *
     * @return int
     */
    public function synchronizeCounter(string $secret, string $code, int $counter, int $lookahead = 10) : int
    {
        $codeLength = strlen($code);
        $candidates = $this->generateEventBasedCodes($secret, $counter, $lookahead + 1, $codeLength);

        foreach ($candidates as $offset => $candidate) {
            if ($candidate === $code) {
                return $counter + $offset;
            }
        }

        throw new Exception\CounterSynchronizationFailed($counter, $code, $lookahead, $counter + $lookahead);
    }


    public function verifyEventBasedCode(string $secret, string $code, int $counter) : bool
    {
        return $this->codeVerifier->verifyEventBasedCode($secret, $code, $counter);
    }


    public function verifyTimeBasedCode(string $secret, string $code) : bool
    {
        return $this->codeVerifier->verifyTimeBasedCode($secret, $code);
    }


    public function generateBarCode(Data $data, Options $options = null) : Barcode
    {
        return $this->barcodeGenerator->generateBarcode($data, $options);
    }


    public function generateTimeBasedBarcode(string $secret, string $accountName, Options $options = null) : Barcode
    {
        $windowLength = $this->clock()->windowLength();
        $algorithm    = $this->algorithm();

        $data = new TimeBasedData($secret, $this->issuer, $accountName, $this->digitCount, $windowLength, $algorithm);

        return $this->generateBarcode($data, $options);
    }


    public function generateEventBasedBarcode(
        string $secret,
        string $accountName,
        Options $options = null,
        $counter = 0
    ) : Barcode {
        $data = new EventBasedData($secret, $this->issuer, $accountName, $this->digitCount, $counter);

        return $this->generateBarcode($data, $options);
    }


    public function algorithm() : Algorithm
    {
        return $this->codeGenerator->algorithm();
    }


    public function clock() : Clock
    {
        return $this->codeGenerator->clock();
    }
}
