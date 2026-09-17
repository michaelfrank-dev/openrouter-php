<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

/**
 * Class ProviderSortPartition
 *
 * String-tolerant wrapper for provider sort partition configuration.
 *
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class ProviderSortPartition
{
    public const MODEL = 'model';
    public const NONE = 'none';

    /**
     * ProviderSortPartition constructor.
     *
     * @param string $value
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Creates an instance of ProviderSortPartition from a raw string.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
