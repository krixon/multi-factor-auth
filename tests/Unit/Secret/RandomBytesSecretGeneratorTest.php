<?php

namespace Krixon\MultiFactorAuthTests\Unit\Secret;

use Krixon\MultiFactorAuth\Codec\PassThroughCodec;
use Krixon\MultiFactorAuth\Secret\Exception\SecretGenerationFailed;
use Krixon\MultiFactorAuth\Secret\RandomBytesSecretGenerator;
use Krixon\MultiFactorAuthTests\TestCase;
use phpmock\phpunit\PHPMock;

class RandomBytesSecretGeneratorTest extends TestCase
{
    use PHPMock;

    /**
     * @var RandomBytesSecretGenerator
     */
    private $generator;


    protected function setUp()
    {
        parent::setUp();

        $this->generator = new RandomBytesSecretGenerator(new PassThroughCodec());
    }


    public function testGeneratesSecretsWithSpecifiedByteCount()
    {
        for ($i = 8; $i <= 256; $i += 8) {
            static::assertByteCount($i, $this->generator->generateSecret($i));
        }
    }


    /**
     * @runInSeparateProcess
     */
    public function testThrowsExpectedExceptionWhenGenerationFails()
    {
        $randomBytes = $this->getFunctionMock('Krixon\MultiFactorAuth\Secret', 'random_bytes');
        $randomBytes->expects($this->any())->willThrowException(new \Exception);

        $this->expectException(SecretGenerationFailed::class);

        $this->generator->generateSecret(1);
    }
}
