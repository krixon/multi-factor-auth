<?php

namespace Krixon\MultiFactorAuthTests\Unit\Code;

use Krixon\MultiFactorAuth\Clock\StoppedClock;
use Krixon\MultiFactorAuth\Code\CodeGenerator;
use Krixon\MultiFactorAuth\Code\StandardCodeVerifier;
use Krixon\MultiFactorAuthTests\TestCase;

class StandardCodeVerifierTest extends TestCase
{
    /**
     * @dataProvider minimumCodeLengthNotSatisfiedProvider
     *
     * @param int $minimum
     */
    public function testFailsIfMinimumCodeLengthNotSatisfied(int $minimum, string $code) : void
    {
        $generator = $this->createMock(CodeGenerator::class);
        $clock     = new StoppedClock(86400);

        $generator->method('clock')->willReturn($clock);
        $generator->method('generateTimeBasedCode')->willReturn($code);

        $verifier  = new StandardCodeVerifier($generator, 1, $minimum);

        static::assertFalse($verifier->verifyTimeBasedCode('secret', $code));
        static::assertFalse($verifier->verifyEventBasedCode('secret', $code, 0));
    }


    public function minimumCodeLengthNotSatisfiedProvider() : \Generator
    {
        foreach (range(1, 10) as $minimum) {
            yield [$minimum, str_repeat(1, $minimum - 1)];
        }
    }
}
