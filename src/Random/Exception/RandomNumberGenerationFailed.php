<?php

namespace Krixon\MultiFactorAuth\Random\Exception;

class RandomNumberGenerationFailed extends \RuntimeException implements RandomException
{
    public function __construct(\Throwable $previous = null)
    {
        parent::__construct('Random number generation failed.', 0, $previous);
    }
}
