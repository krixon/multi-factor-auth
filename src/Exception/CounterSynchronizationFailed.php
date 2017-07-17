<?php

namespace Krixon\MultiFactorAuth\Exception;

class CounterSynchronizationFailed extends \RuntimeException implements MultiFactorAuthException
{
    public function __construct(int $counter, string $code, int $lookahead, int $maxCounter)
    {
        $message = sprintf(
            "Counter %d could not be synchronized using code %s and a lookahead of %d: Perhaps the client's" .
            " counter is further ahead than the maximum tested counter value of %d?",

            $counter,
            $code,
            $lookahead,
            $maxCounter
        );

        parent::__construct($message);
    }
}
