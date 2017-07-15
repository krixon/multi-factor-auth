<?php

namespace Krixon\MultiFactorAuth\Hash;

interface AlgorithmProvider
{
    public function algorithm() : string;
}
