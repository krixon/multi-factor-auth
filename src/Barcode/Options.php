<?php

namespace Krixon\MultiFactorAuth\Barcode;

class Options
{
    public const DEFAULT_WIDTH  = 200;
    public const DEFAULT_HEIGHT = 200;

    private $foregroundColor;
    private $backgroundColor;
    private $width;
    private $height;
    private $sourceCharset;
    private $targetCharset;
    private $errorCorrectionLevel;
    private $margin;
    private $format;


    /**
     * @param int|null    $width
     * @param int|null    $height
     * @param string|null $format
     * @param int|null    $margin
     * @param string|null $errorCorrectionLevel
     * @param string|null $foregroundColor
     * @param string|null $backgroundColor
     * @param string|null $sourceCharset
     * @param string|null $targetCharset
     */
    public function __construct(
        int $width = null,
        int $height = null,
        string $format = null,
        int $margin = null,
        string $errorCorrectionLevel = null,
        string $foregroundColor = null,
        string $backgroundColor = null,
        string $sourceCharset = null,
        string $targetCharset = null
    ) {
        $this->width                = $width;
        $this->height               = $height;
        $this->format               = $format;
        $this->margin               = $margin;
        $this->errorCorrectionLevel = $errorCorrectionLevel;
        $this->foregroundColor      = $foregroundColor;
        $this->backgroundColor      = $backgroundColor;
        $this->sourceCharset        = $sourceCharset;
        $this->targetCharset        = $targetCharset;
    }


    public static function default()
    {
        return new static();
    }


    public function foregroundColor() : ?string
    {
        return $this->foregroundColor;
    }


    public function withForegroundColor(string $foregroundColor)
    {
        $instance = clone $this;

        $instance->foregroundColor = $foregroundColor;

        return $instance;
    }


    public function backgroundColor() : ?string
    {
        return $this->backgroundColor;
    }


    public function withBackgroundColor(string $backgroundColor)
    {
        $instance = clone $this;

        $instance->backgroundColor = $backgroundColor;

        return $instance;
    }


    public function width() : ?int
    {
        return $this->width;
    }


    public function withWidth(int $width)
    {
        $instance = clone $this;

        $instance->width = $width;

        return $instance;
    }


    public function height() : ?int
    {
        return $this->height;
    }


    public function withHeight(int $height)
    {
        $instance = clone $this;

        $instance->height = $height;

        return $instance;
    }


    public function sourceCharset() : ?string
    {
        return $this->sourceCharset;
    }


    public function withSourceCharset(string $sourceCharset)
    {
        $instance = clone $this;

        $instance->sourceCharset = $sourceCharset;

        return $instance;
    }


    public function targetCharset() : ?string
    {
        return $this->targetCharset;
    }


    public function withTargetCharset(string $targetCharset)
    {
        $instance = clone $this;

        $instance->targetCharset = $targetCharset;

        return $instance;
    }


    public function errorCorrectionLevel() : ?string
    {
        return $this->errorCorrectionLevel;
    }


    public function withErrorCorrectionLevel(string $errorCorrectionLevel)
    {
        $instance = clone $this;

        $instance->errorCorrectionLevel = $errorCorrectionLevel;

        return $instance;
    }


    public function margin() : ?int
    {
        return $this->margin;
    }


    public function withMargin(int $margin)
    {
        $instance = clone $this;

        $instance->margin = $margin;

        return $instance;
    }


    public function format() : ?string
    {
        return $this->format;
    }


    public function withFormat(string $format)
    {
        $instance = clone $this;

        $instance->format = $format;

        return $instance;
    }


    /**
     * Unions the options in this instance with those in another instance.
     *
     * If this instance has a value for an option it will be retained and the value from $other will be discarded.
     *
     * Note that a new third instance is returned containing the result; neither this instance or $other is modified.
     *
     * @param Options $other
     *
     * @return Options
     */
    public function union(self $other) : self
    {
        return new static(
            $this->width                ?? $other->width,
            $this->height               ?? $other->height,
            $this->format               ?? $other->format,
            $this->margin               ?? $other->margin,
            $this->errorCorrectionLevel ?? $other->errorCorrectionLevel,
            $this->foregroundColor      ?? $other->foregroundColor,
            $this->backgroundColor      ?? $other->backgroundColor,
            $this->sourceCharset        ?? $other->sourceCharset
        );
    }


    public function equals(self $other) : bool
    {
        if ($this === $other) {
            return true;
        }

        if (!$other instanceof static) {
            return false;
        }

        return $other->width                === $this->width
            && $other->height               === $this->height
            && $other->format               === $this->format
            && $other->margin               === $this->margin
            && $other->errorCorrectionLevel === $this->errorCorrectionLevel
            && $other->foregroundColor      === $this->foregroundColor
            && $other->backgroundColor      === $this->backgroundColor
            && $other->sourceCharset        === $this->sourceCharset;
    }
}
