<?php

namespace Krixon\MultiFactorAuthTests\Unit\Barcode;

use Krixon\MultiFactorAuth\Barcode\GenericBarcode;
use Krixon\MultiFactorAuthTests\TestCase;

class GenericBarcodeTest extends TestCase
{
    public function testImageData()
    {
        $barcode = new GenericBarcode('abc123', 'image/jpeg');

        static::assertSame('abc123', $barcode->imageData());
    }


    public function testMimeType()
    {
        $barcode = new GenericBarcode('abc123', 'image/jpeg');

        static::assertSame('image/jpeg', $barcode->mimeType());
    }


    public function testDataUri()
    {
        $barcode = new GenericBarcode('abc123', 'image/jpeg');

        static::assertSame('data:image/jpeg;base64,YWJjMTIz', $barcode->dataUri());
    }
}
