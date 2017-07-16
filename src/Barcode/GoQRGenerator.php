<?php

namespace Krixon\MultiFactorAuth\Barcode;

class GoQRGenerator extends HTTPGenerator
{
    use GeneratesQRCodes;


    protected function url(Data $data, Options $options) : string
    {
        return sprintf(
            'https://api.qrserver.com/v1/create-qr-code/?data=%s&size=%s&charset-source=%s&ecc=%s&format=%s' .
            '&color=%s&bgcolor=%s',

            $this->generateKeyURI($data),
            $this->size($options),
            $this->sourceCharset($options),
            $this->errorCorrectionLevel($options),
            $this->format($options),
            $this->foregroundColor($options),
            $this->backgroundColor($options)
        );
    }


    protected function mimeType(Options $options) : string
    {
        return 'image/' . $this->format($options);
    }


    private function size(Options $options) : string
    {
        $width  = $options->width() ?? Options::DEFAULT_WIDTH;
        $height = $options->height() ?? Options::DEFAULT_HEIGHT;

        return sprintf('%dx%d', $width, $height);
    }


    private function sourceCharset(Options $options) : string
    {
        return $options->sourceCharset() ?? 'UTF-8';
    }


    private function format(Options $options)
    {
        $format = $options->format();

        if (null !== $format) {
            $format = strtolower($format);
            if (in_array($format, ['png', 'gif', 'jpeg', 'jpg', 'svg', 'eps'], true)) {
                return $format;
            }
        }

        return 'png';
    }


    private function foregroundColor(Options $options)
    {
        return ltrim($options->foregroundColor() ?? 'FF0000', '#');
    }


    private function backgroundColor(Options $options)
    {
        return ltrim($options->backgroundColor() ?? '000000', '#');
    }
}
