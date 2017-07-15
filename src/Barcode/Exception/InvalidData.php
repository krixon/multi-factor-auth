<?php

namespace Krixon\MultiFactorAuth\Barcode\Exception;

class InvalidData extends \DomainException implements BarcodeException
{
    public function __construct(string $description, \Throwable $previous = null)
    {
        parent::__construct("Invalid barcode data encountered: $description.", 0, $previous);
    }
}
