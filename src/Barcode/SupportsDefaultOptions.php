<?php

namespace Krixon\MultiFactorAuth\Barcode;

trait SupportsDefaultOptions
{
    protected $defaults;


    protected function resolveOptions(?Options $provided) : Options
    {
        if (!$provided) {
            return $this->defaults ?? Options::default();
        }

        if (!$this->defaults) {
            return $provided;
        }

        return $provided->union($this->defaults);
    }
}
