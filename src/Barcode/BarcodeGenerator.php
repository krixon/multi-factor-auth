<?php

namespace Krixon\MultiFactorAuth\Barcode;

interface BarcodeGenerator
{
    public function generateBarcode(Data $data, Options $options = null) : Barcode;
}
