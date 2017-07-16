Multi Factor Authentication
===========================

[![Build Status](https://travis-ci.org/krixon/multi-factor-auth.svg?branch=master)](https://travis-ci.org/krixon/multi-factor-auth)
[![Coverage Status](https://coveralls.io/repos/github/krixon/multi-factor-auth/badge.svg?branch=master)](https://coveralls.io/github/krixon/multi-factor-auth?branch=master)
[![Latest Stable Version](https://poser.pugx.org/krixon/multi-factor-auth/v/stable)](https://packagist.org/packages/krixon/multi-factor-auth)
[![Latest Unstable Version](https://poser.pugx.org/krixon/multi-factor-auth/v/unstable)](https://packagist.org/packages/krixon/multi-factor-auth)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cd22d4e6-4409-4491-9e6f-56cdf7bf8964/big.png)](https://insight.sensiolabs.com/projects/cd22d4e6-4409-4491-9e6f-56cdf7bf8964)

A library for generating and verifying the codes used in multi-factor authentication systems.

Features:

- Time-based ([TOTP](https://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm)) code generation and verification.
- Event-based ([HOTP](https://en.wikipedia.org/wiki/HMAC-based_One-time_Password_Algorithm)) code generation and verification.
- Barcode generation for easy client setup.

This library implements the following RFCs:

- [RFC4226 - HOTP: An HMAC-Based One-Time Password Algorithm](https://tools.ietf.org/html/rfc4226)
- [RFC6238 - TOTP: Time-Based One-Time Password Algorithm](https://tools.ietf.org/html/rfc6238)

It has been tested against the following multi-factor authentication tools:

- [Google Authenticator](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2) (TOTP + HOTP)
- [Authy](https://authy.com/) (TOTP + HOTP)

# Prerequisites

- PHP 7.1+

# Installation
## Install via composer

To install this library with Composer, run the following command:

```sh
$ composer require krixon/multi-factor-auth
```

You can see this library on [Packagist](https://packagist.org/packages/krixon/multi-factor-auth).

## Install from source

```sh
# HTTP
$ git clone https://github.com/krixon/multi-factor-auth.git
# SSH
$ git clone git@github.com:krixon/multi-factor-auth.git
```

# Quick Start

Let's say you have a server side application which you want to protect using multi-factor authentication.

There are three main steps involved:

1. Generate a secret which is shared between the server and the user.
2. Configure a client application (such as Google Authenticator) with the shared secret.
3. Verify codes generated by the client application whenever the user needs to authenticate.

This library makes these steps easy.

The quickest way to get up and running is to create a new instance of the `MultiFactorAuth` class. This takes
various arguments to its constructor, but there is a static factory provided which creates an instance with sensible
and secure defaults. The only thing you need to provide is an "issuer" string. This is just a label which
identifies the provider or service managing a user's account - i.e. your application.

```php
<?php

use Krixon\MultiFactorAuth\MultiFactorAuth;

$mfa = MultiFactorAuth::default('Example Issuer');
```

Next you need to generate the shared secret. By default the code below will generate a 160-bit, base32-encoded string:

```php
$secret = $mfa->generateSecret();
```

In order for the user to configure their client application, they need to enter the secret that was just generated.
Often the user's client application will be running on their mobile phone. Entering a 160-bit secret by hand is
certainly possible, but we can make it easier by providing the user with a barcode to scan. This barcode contains all
of the information required to configure the client.

When generating a barcode you must also provide an account identifier. This can be any string which allows the user
to distinguish between multiple accounts in their client application. A good value for this is the user's email
address.


```php
$barcode = $mfa->generateTimeBasedBarcode($secret, 'jane.doe@example.com');
```

The `generateTimeBasedBarcode()` method returns a `Barcode` instance. This can be used to ultimately render the
image, for example on a webpage:

```php
<img src="<?= $barcode->dataUri() ?>">
```

Once the user has scanned the barcode, they should be prompted to enter a code which can be verified to determine
that the configuration process was successful.

```php
$verified = $mfa->verifyTimeBasedCode($code, $secret);
```

If the code is verified successfully, the secret can be securely persisted on the server, for example in a database.

From now on, when the user authenticates they should be prompted to enter a code along with their other credentials
such as username and password. This code should be verified using the stored shared secret and authentication denied
if verification fails.

# Generating Backup Codes

If a user loses their device or otherwise cannot generate codes, you can allow them to login via a pre-generated
backup code. Event-based ([HOTP](https://en.wikipedia.org/wiki/HMAC-based_One-time_Password_Algorithm)) codes are
perfect for this.

The following example generates 10 backup codes which the user can write down or otherwise store.

```php
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
```

# Sandbox

There is a simple sandbox script in the `examples` directory which can be used to generate secrets and barcodes and
to verify codes generated by a client application.

The sandbox can be run with the PHP built-in webserver. Make sure to specify the correct path to the `examples`
directory:

```bash
php -S localhost:8080 -t /path/to/krixon/multi-factor-auth/examples
```

You can now visit [http://localhost:8080/sandbox.php](http://localhost:8080/sandbox.php) to use the sandbox.

# TODO

- More automated tests.
- Expand documentation.
- Verify support for [RFC6287 - OCRA: OATH Challenge-Response Algorithm](https://tools.ietf.org/html/rfc6287).
