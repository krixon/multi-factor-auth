<?php

namespace Krixon\MultiFactorAuth\Barcode;

trait GeneratesQRCodes
{
    protected function errorCorrectionLevel(Options $options) : string
    {
        $level = $options->errorCorrectionLevel();

        if (null !== $level) {
            $level = strtoupper($level);
            if (in_array($level, ['L', 'M', 'Q', 'H'], true)) {
                return $level;
            }
        }

        return 'L';
    }
}
