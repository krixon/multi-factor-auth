<?php

namespace Krixon\MultiFactorAuth\HTTP;

interface Client
{
    public function get(string $url) : string;
}
