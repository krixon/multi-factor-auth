<?php

namespace Krixon\MultiFactorAuth\Barcode;

class GoogleQRGenerator extends HTTPGenerator
{
    use GeneratesQRCodes;


    protected function url(Data $data, Options $options) : string
    {
        $options = $this->resolveOptions($options);

        /** @noinspection SpellCheckingInspection */
        return sprintf(
            'https://chart.googleapis.com/chart?cht=qr&chs=%s&chld=%s|%d&chl=%s',
            $this->size($options),
            $this->errorCorrectionLevel($options),
            $this->margin($options),
            $this->generateKeyURI($data)
        );
    }


    protected function mimeType(Options $options) : string
    {
        return 'image/png';
    }


    private function size(Options $options) : string
    {
        $width  = $options->width() ?? Options::DEFAULT_WIDTH;
        $height = $options->height() ?? Options::DEFAULT_HEIGHT;

        return sprintf('%dx%d', $width, $height);
    }


    private function margin(Options $options) : int
    {
        return $options->margin() ?? 1;
    }
}
