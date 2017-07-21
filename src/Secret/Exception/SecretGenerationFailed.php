<?php

namespace Krixon\MultiFactorAuth\Secret\Exception;

class SecretGenerationFailed extends \RuntimeException implements SecretException
{
    public function __construct(\Throwable $previous = null)
    {
        parent::__construct('Secret generation failed.', 0, $previous);
    }
}
