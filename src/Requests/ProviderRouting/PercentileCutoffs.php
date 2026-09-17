<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

/**
 * Class PercentileCutoffs
 *
 * Configures percentile performance constraints for provider preferences.
 *
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class PercentileCutoffs implements \JsonSerializable
{
    /**
     * PercentileCutoffs constructor.
     *
     * @param float|null $p50
     * @param float|null $p75
     * @param float|null $p90
     * @param float|null $p99
     */
    public function __construct(
        public ?float $p50 = null,
        public ?float $p75 = null,
        public ?float $p90 = null,
        public ?float $p99 = null,
    ) {
    }

    /**
     * Converts properties to serializable array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'p50' => $this->p50,
            'p75' => $this->p75,
            'p90' => $this->p90,
            'p99' => $this->p99,
        ], static fn(mixed $value): bool => $value !== null);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
