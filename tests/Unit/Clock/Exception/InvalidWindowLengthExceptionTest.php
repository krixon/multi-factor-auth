<?php

namespace Krixon\MultiFactorAuthTests\Unit\Clock\Exception;

use Krixon\MultiFactorAuth\Clock\Exception\InvalidWindowLength;
use Krixon\MultiFactorAuthTests\TestCase;

class InvalidWindowLengthExceptionTest extends TestCase
{
    public function testMessageIncludesInvalidWindowLength() : void
    {
        $e = new InvalidWindowLength(12345);

        static::assertRegExp('/\b12345\b/', $e->getMessage());
    }
}
