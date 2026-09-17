<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

/**
 * Class SearchContextSize
 *
 * String-tolerant wrapper for SearchContextSize option.
 *
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class SearchContextSize
{
    public const LOW = 'low';
    public const MEDIUM = 'medium';
    public const HIGH = 'high';

    /**
     * SearchContextSize constructor.
     *
     * @param string $value
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Creates an instance of SearchContextSize from a raw string.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
