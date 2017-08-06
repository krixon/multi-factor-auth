<?php

namespace Krixon\MultiFactorAuth\OCRA;

use Krixon\MultiFactorAuth\CountsBytes;
use Krixon\MultiFactorAuth\Hash\Hasher;

class DataInput
{
    use CountsBytes;

    private $counter;

    private $challenge;

    private $password;

    private $sessionData;

    private $window;


    public function __construct(
        Challenge $challenge,
        Password $password = null,
        SessionData $sessionData = null,
        int $counter = null,
        Window $window = null
    ) {
        $this->challenge   = $challenge;
        $this->password    = $password;
        $this->counter     = $counter;
        $this->sessionData = $sessionData;
        $this->window      = $window;
    }


//    public static function fromString(string $string) : self
//    {
//        $useCounter        = false;
//        $challengeFormat   = null;
//        $passwordAlgorithm = null;
//        $sessionByteCount  = null;
//        $windowSize        = null;
//
//        foreach (explode('-', $string) as $value) {
//            switch ($value[0]) {
//                case 'C':
//                    $useCounter = true;
//                    break;
//                case 'Q':
//                    $challengeFormat = ChallengeFormat::fromString($value);
//                    break;
//                case 'P':
//                    $passwordAlgorithm = Algorithm::fromString(substr($value, 1));
//                    break;
//                case 'S':
//                    $sessionByteCount = (int)substr($value, 1);
//                    break;
//                case 'T':
//                    $windowSize = WindowSize::fromString($value);
//                    break;
//            }
//        }
//
//        return new static($challengeFormat, $passwordAlgorithm, $sessionByteCount, $useCounter, $windowSize);
//    }


    public function __toString()
    {
        $values = [
            null !== $this->counter ? 'C' : false,
            $this->challenge,
            $this->password,
            $this->sessionData,
            $this->window,
        ];

        return implode('-', array_filter($values));
    }


    public function toMessage() : string
    {
        $message = '';

        if (null !== $this->counter) {
            $message .= pack('H*', str_pad($this->counter, 16, '0', STR_PAD_LEFT));
        }

        $message .= $this->challenge->toMessage();

        if ($this->password) {
            $message .= $this->password->toMessage();
        }

        if ($this->sessionData) {
            $message .= $this->sessionData->toMessage();
        }

        if ($this->window) {
            $message .= $this->window->toMessage();
        }

        return $message;
    }


    public function counter() : ?int
    {
        return $this->counter;
    }


    public function challenge() : Challenge
    {
        return $this->challenge;
    }


    public function password() : ?Password
    {
        return $this->password;
    }


    public function sessionData() : ?SessionData
    {
        return $this->sessionData;
    }


    public function window() : ?Window
    {
        return $this->window;
    }
}
