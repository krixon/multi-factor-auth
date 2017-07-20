<?php

namespace Krixon\MultiFactorAuthTests\Unit\Secret;

use Krixon\MultiFactorAuth\Codec\PassThroughCodec;
use Krixon\MultiFactorAuth\Random\RandomBytes;
use Krixon\MultiFactorAuth\Secret\RNGSecretGenerator;
use Krixon\MultiFactorAuthTests\TestCase;

class RNGSecretGeneratorTest extends TestCase
{
    public function testGeneratesCodesWithSpecifiedBitCount()
    {
        for ($i = 8; $i <= 256; $i += 8) {
            $generator = new RNGSecretGenerator(new RandomBytes(), new PassThroughCodec());
            $secret    = $generator->generateSecret($i);

            static::assertBitCount($i, $secret);
        }
    }
}
