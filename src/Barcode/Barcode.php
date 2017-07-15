<?php

namespace Krixon\MultiFactorAuth\Barcode;

interface Barcode
{
    public function imageData() : string;
    public function mimeType() : string;
    public function dataUri() : string;
}
