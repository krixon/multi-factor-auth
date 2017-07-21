<?php

namespace Krixon\MultiFactorAuthTests\Unit\Codec;

use Krixon\MultiFactorAuth\Codec\PassThroughCodec;
use Krixon\MultiFactorAuthTests\TestCase;

class PassThroughCodecTest extends TestCase
{
    public function testEncodesCorrectly()
    {
        $input  = 'abc123=!.';
        $output = (new PassThroughCodec())->encode($input);

        static::assertSame($input, $output);
    }


    public function testDecodesCorrectly()
    {
        $input  = 'abc123=!.';
        $output = (new PassThroughCodec())->decode($input);

        static::assertSame($input, $output);
    }
}
