<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class ModelListResponse
 *
 * Holds lists of model capabilities and query response metadata.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ModelListResponse
{
    /**
     * ModelListResponse constructor.
     *
     * @param string $object
     * @param array<ModelInfo> $data
     * @param ResponseMetadata $metadata
     */
    public function __construct(
        public string $object,
        public array $data,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build ModelListResponse from payload array and metadata.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata|null $metadata
     * @return self
     */
    public static function fromArray(array $data, ?ResponseMetadata $metadata = null): self
    {
        $infos = [];
        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $item) {
                if (is_array($item)) {
                    $infos[] = ModelInfo::fromArray($item);
                }
            }
        }

        $object = $data['object'] ?? null;

        return new self(
            object: is_string($object) ? $object : '',
            data: $infos,
            metadata: $metadata ?? new ResponseMetadata()
        );
    }
}
