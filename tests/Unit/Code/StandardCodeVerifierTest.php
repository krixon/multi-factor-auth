<?php

namespace Krixon\MultiFactorAuthTests\Unit\Code;

use Krixon\MultiFactorAuth\Clock\StoppedClock;
use Krixon\MultiFactorAuth\Code\CodeGenerator;
use Krixon\MultiFactorAuth\Code\StandardCodeVerifier;
use Krixon\MultiFactorAuthTests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

class StandardCodeVerifierTest extends TestCase
{
    /**
     * @var CodeGenerator|MockObject
     */
    private $generator;


    /**
     * @throws ReflectionException
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->generator = $this->createMock(CodeGenerator::class);
    }


    /**
     * @dataProvider minimumCodeLengthNotSatisfiedProvider
     */
    public function testFailsIfMinimumCodeLengthNotSatisfied(string $submittedCode, string $generatedCode) : void
    {
        $clock     = new StoppedClock(86400);

        $this->generator->method('clock')->willReturn($clock);
        $this->generator->method('generateTimeBasedCode')->willReturn($generatedCode);

        $verifier  = new StandardCodeVerifier($this->generator, 1);

        static::assertFalse($verifier->verifyTimeBasedCode('secret', $submittedCode));
        static::assertFalse($verifier->verifyEventBasedCode('secret', $submittedCode, 0));
    }


    public function minimumCodeLengthNotSatisfiedProvider() : array
    {
        return [
            ['1', '123456'],
            ['12', '123456'],
            ['123', '123456'],
            ['1234', '123456'],
            ['12345', '123456'],
            ['1234567', '123456'],
            ['12345678', '123456'],
            ['123455', '123456'],
            ['123457', '123456'],
        ];
    }
}
