<?php

namespace Krixon\MultiFactorAuth\Barcode;

use Krixon\MultiFactorAuth\HTTP\Client;

class GoogleQRGenerator extends HTTPGenerator
{
    private $defaults;


    public function __construct(Client $httpClient, Options $defaults = null)
    {
        parent::__construct($httpClient);

        $this->defaults = $defaults ?: Options::default();
    }


    protected function url(Data $data, Options $options) : string
    {
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
        $width  = $options->width() ?? $this->defaults->width() ?? 200;
        $height = $options->height() ?? $this->defaults->height() ?? 200;

        return sprintf('%dx%d', $width, $height);
    }


    private function errorCorrectionLevel(Options $options) : string
    {
        $level = $options->errorCorrectionLevel() ?? $this->defaults->errorCorrectionLevel();
        $level = strtoupper($level);

        if (in_array($level, ['L', 'M', 'Q', 'H'], true)) {
            return $level;
        }

        return 'L';
    }


    private function margin(Options $options) : int
    {
        return $options->margin() ?? $this->defaults->margin() ?? 1;
    }


    private function generateKeyURI(Data $data) : string
    {
        $parameters = [
            'secret'    => $data->secret(),
            'issuer'    => $data->issuer(),
            'digits'    => $data->digitCount(),
        ];

        if ($data instanceof TimeBasedData) {
            $type        = 'totp';
            $parameters += [
                'period'    => $data->windowLength(),
                'algorithm' => strtoupper($data->algorithm()),
            ];
        } elseif ($data instanceof EventBasedData) {
            $type        = 'hotp';
            $parameters += [
                'counter'   => $data->counter(),
                'algorithm' => 'SHA1',
            ];
        } else {
            throw new Exception\InvalidData(sprintf(
                'GoogleQRGenerator only supports TimeBasedData and EventBasedData (encountered %s)',
                get_class($data)
            ));
        }

        /** @noinspection SpellCheckingInspection */
        $data = sprintf(
            'otpauth://%s/%s?%s',
            $type,
            $this->label($data),
            http_build_query($parameters, '', '&', PHP_QUERY_RFC3986)
        );

        return rawurlencode($data);
    }


    private function label(Data $data) : string
    {
        return rawurlencode(implode(':', array_filter([$data->issuer(), $data->accountName()])));
    }
}
