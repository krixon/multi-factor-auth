<?php

namespace Krixon\MultiFactorAuth\Barcode;

trait GeneratesKeyURIsFromData
{
    protected function generateKeyURI(Data $data) : string
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
                get_class($this) . ' only supports TimeBasedData and EventBasedData (encountered %s)',
                get_class($data)
            ));
        }

        $queryString = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
        $data        = sprintf('otpauth://%s/%s?%s', $type, $this->label($data), $queryString);

        return rawurlencode($data);
    }


    private function label(Data $data) : string
    {
        return rawurlencode(implode(':', array_filter([$data->issuer(), $data->accountName()])));
    }
}
