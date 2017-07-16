<?php

use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\MultiFactorAuth;

$mfa     = MultiFactorAuth::default('Test Issuer');
$secret  = (new Base32Codec())->encode('12345678901234567890');
$counter = 42; // TODO: Retrieve the real counter from the DB or wherever it is stored.

// $codes is an array of Code objects.
$codes  = $mfa->generateBackupCodes($secret, $counter, 10);

foreach ($codes as $code) {
    // Do something with the backup code.
    $code->toString();  // zero-padded 6-digit code.
    $code->toString(8); // zero-padded 8-digit code.
    $code->toHex();     // Hexadecimal representation of the 31 bit value.
    $code->toDecimal(); // Decimal representation of the 31 bit value.
    $code->toBinary();  // 31 bits of binary data.
}
