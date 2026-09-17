<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class ImagePricingEntry
 *
 * Represents one billable pricing line for an image provider.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ImagePricingEntry
{
    /**
     * ImagePricingEntry constructor.
     *
     * @param string $billable The billable item type (e.g. 'output_image')
     * @param string $unit The pricing unit (e.g. 'image')
     * @param float $costUsd Cost in USD
     * @param string|null $variant Optional pricing variant descriptor
     */
    public function __construct(
        public string $billable,
        public string $unit,
        public float $costUsd,
        public ?string $variant = null,
    ) {
    }

    /**
     * Factory to build ImagePricingEntry from a payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $billable = isset($data['billable']) && is_string($data['billable']) ? $data['billable'] : '';
        $unit = isset($data['unit']) && is_string($data['unit']) ? $data['unit'] : '';
        $costUsd = isset($data['cost_usd']) && is_numeric($data['cost_usd']) ? (float)$data['cost_usd'] : 0.0;
        $variant = isset($data['variant']) && is_string($data['variant']) ? $data['variant'] : null;

        return new self(
            billable: $billable,
            unit: $unit,
            costUsd: $costUsd,
            variant: $variant
        );
    }
}
