<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

/**
 * Class ProviderSortConfig
 *
 * Configures detailed provider sorting with a partition boundary.
 *
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class ProviderSortConfig implements \JsonSerializable
{
    /**
     * ProviderSortConfig constructor.
     *
     * @param ProviderSort $by
     * @param ProviderSortPartition $partition
     */
    public function __construct(
        public ProviderSort $by,
        public ProviderSortPartition $partition,
    ) {
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode.
     *
     * @return array{by: string, partition: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'by' => $this->by->value,
            'partition' => $this->partition->value,
        ];
    }
}
