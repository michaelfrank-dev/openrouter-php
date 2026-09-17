<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

/**
 * Class UserLocation
 *
 * Configures approximate user location properties to geographically bias search engine results.
 * Note: This parameter only geographically biases the search engine results — it does not inject location context into the model's reasoning.
 * Currently only supported by native provider search; ignored with Exa, Firecrawl, Parallel, and Perplexity.
 *
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class UserLocation implements \JsonSerializable
{
    /**
     * UserLocation constructor.
     *
     * @param string|null $type The location representation type (defaults to 'approximate').
     * @param string|null $city Approximate city name (e.g. 'Recife').
     * @param string|null $region Approximate region or state name (e.g. 'Pernambuco').
     * @param string|null $country Approximate country name or code (e.g. 'BR').
     * @param string|null $timezone Approximate timezone (e.g. 'America/Recife').
     */
    public function __construct(
        public ?string $type = 'approximate',
        public ?string $city = null,
        public ?string $region = null,
        public ?string $country = null,
        public ?string $timezone = null,
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
            'type' => $this->type,
            'city' => $this->city,
            'region' => $this->region,
            'country' => $this->country,
            'timezone' => $this->timezone,
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
