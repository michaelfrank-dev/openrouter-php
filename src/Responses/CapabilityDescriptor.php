<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class CapabilityDescriptor
 *
 * Represents a typed capability descriptor for a supported request parameter.
 * Handles boolean, enum, and numeric range capabilities.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class CapabilityDescriptor
{
    /**
     * CapabilityDescriptor constructor.
     *
     * @param string $type The type of capability: 'boolean', 'enum', or 'range'
     * @param array<string>|null $values Allowed values (only valid if type is 'enum')
     * @param float|null $min Minimum value (only valid if type is 'range')
     * @param float|null $max Maximum value (only valid if type is 'range')
     */
    public function __construct(
        public string $type,
        public ?array $values = null,
        public ?float $min = null,
        public ?float $max = null,
    ) {
    }

    /**
     * Factory to build CapabilityDescriptor from a payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $type = isset($data['type']) && is_string($data['type']) ? $data['type'] : 'boolean';
        $values = isset($data['values']) && is_array($data['values']) ? array_map('strval', $data['values']) : null;
        $min = isset($data['min']) && is_numeric($data['min']) ? (float)$data['min'] : null;
        $max = isset($data['max']) && is_numeric($data['max']) ? (float)$data['max'] : null;

        return new self(
            type: $type,
            values: $values,
            min: $min,
            max: $max
        );
    }
}
