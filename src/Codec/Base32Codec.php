<?php

namespace Krixon\MultiFactorAuth\Codec;

class Base32Codec implements Codec
{
    private const DICTIONARY = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=';

    private const MAP = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
        'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z', '2', '3', '4', '5', '6', '7',
        '='
    ];

    private const REVERSE_MAP = [
        'A' => 0,  'B' => 1,  'C' => 2,  'D' => 3,  'E' => 4,  'F' => 5,  'G' => 6,  'H' => 7,
        'I' => 8,  'J' => 9,  'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15,
        'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19, 'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23,
        'Y' => 24, 'Z' => 25, '2' => 26, '3' => 27, '4' => 28, '5' => 29, '6' => 30, '7' => 31,
        '=' => 32
    ];

    private $pad;


    public function __construct($pad = false)
    {
        $this->pad = $pad;
    }


    public function encode(string $data) : string
    {
        if (strlen($data) === 0) {
            return '';
        }

        $buffer = '';
        foreach (str_split($data) as $char) {
            $buffer .= str_pad(base_convert(ord($char), 10, 2), 8, '0', STR_PAD_LEFT);
        }

        $result = '';
        foreach (str_split($buffer, 5) as $block) {
            $result .= self::MAP[base_convert(str_pad($block, 5, '0'), 2, 10)];
        }

        if ($this->pad) {
            $padding = strlen($buffer) % 40;

            if ($padding !== 0) {
                if ($padding === 8) {
                    $result .= str_repeat(self::MAP[32], 6);
                } elseif ($padding === 16) {
                    $result .= str_repeat(self::MAP[32], 4);
                } elseif ($padding === 24) {
                    $result .= str_repeat(self::MAP[32], 3);
                } elseif ($padding === 32) {
                    $result .= self::MAP[32];
                }
            }
        }

        return $result;
    }


    public function decode(string $data) : string
    {
        $length = strlen($data);

        if ($length === 0) {
            return '';
        }

        if ($length !== strspn($data, self::DICTIONARY)) {
            throw new Exception\DecodingFailed($data, 'Invalid base32 string');
        }

        $buffer = '';
        foreach (str_split($data) as $char) {
            if ($char === '=') {
                break;
            }
            $buffer .= str_pad(decbin(self::REVERSE_MAP[$char]), 5, 0, STR_PAD_LEFT);
        }

        $length = strlen($buffer);
        $blocks = str_split(substr($buffer, 0, $length - ($length % 8)), 8);
        $output = '';

        foreach ($blocks as $block) {
            $output .= chr(bindec(str_pad($block, 8, 0, STR_PAD_RIGHT)));
        }

        return $output;
    }
}
