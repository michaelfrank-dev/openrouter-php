<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

/**
 * Class ModelListSort
 *
 * String-tolerant wrapper for sorting options when querying models list.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final readonly class ModelListSort
{
    public const MOST_POPULAR = 'most-popular';
    public const NEWEST = 'newest';
    public const TOP_WEEKLY = 'top-weekly';
    public const PRICING_LOW_TO_HIGH = 'pricing-low-to-high';
    public const PRICING_HIGH_TO_LOW = 'pricing-high-to-low';
    public const CONTEXT_HIGH_TO_LOW = 'context-high-to-low';
    public const THROUGHPUT_HIGH_TO_LOW = 'throughput-high-to-low';
    public const LATENCY_LOW_TO_HIGH = 'latency-low-to-high';

    /**
     * ModelListSort constructor.
     *
     * @param string $value
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Creates an instance of ModelListSort from a raw string.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
