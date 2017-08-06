<?php

namespace Krixon\MultiFactorAuth\OCRA\Exception;

class ChallengeGenerationFailed extends \RuntimeException implements OCRAException
{
    public function __construct(\Throwable $previous = null)
    {
        parent::__construct('Challenge generation failed.', 0, $previous);
    }
}
