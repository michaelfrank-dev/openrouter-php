<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

/**
 * Class ImageResolution
 *
 * String-tolerant wrapper for image generation resolutions.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final readonly class ImageResolution
{
    public const FIVE_TWELVE = '512';
    public const ONE_K = '1K';
    public const TWO_K = '2K';
    public const FOUR_K = '4K';

    /**
     * ImageResolution constructor.
     *
     * @param string $value
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Creates an instance of ImageResolution from a raw string.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
