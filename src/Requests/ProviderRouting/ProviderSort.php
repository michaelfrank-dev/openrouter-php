<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

/**
 * Class ProviderSort
 *
 * String-tolerant wrapper for provider sorting configurations.
 *
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class ProviderSort
{
    public const PRICE = 'price';
    public const THROUGHPUT = 'throughput';
    public const LATENCY = 'latency';
    public const EXACTO = 'exacto';

    /**
     * ProviderSort constructor.
     *
     * @param string $value
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Creates an instance of ProviderSort from a raw string.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
