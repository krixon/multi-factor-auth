<?php

use Krixon\MultiFactorAuth\Codec\Base32Codec;
use Krixon\MultiFactorAuth\MultiFactorAuth;

$mfa     = MultiFactorAuth::default('Test Issuer');
$secret  = (new Base32Codec())->encode('12345678901234567890');
$counter = 42; // Retrieve the real counter from the DB or wherever it is stored.

// $codes is an array of Code objects.
$codes  = $mfa->generateEventBasedCodes($secret, $counter, 10);

foreach ($codes as $code) {
    // Do something with the backup code.
    // Generally you would salt and hash the code and store it in a database. These codes would be checked
    // against the one entered by the user (in addition to checking the current time or event-based code).
    $code->toString();  // zero-padded 6-digit code.
    $code->toString(8); // zero-padded 8-digit code.
    $code->toDecimal(); // Decimal representation of the 31 bit value.
}
