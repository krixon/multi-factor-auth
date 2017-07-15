<?php

namespace Krixon\MultiFactorAuth\Codec\Exception;

class DecodingFailed extends \RuntimeException implements CodecException
{
    public function __construct(string $value, string $message = '', \Throwable $previous = null)
    {
        $message = sprintf(
            "Decoding failed: %s'%s'.",
            $message ? "$message: " : '',
            $value
        );

        parent::__construct($message, 0, $previous);
    }
}
