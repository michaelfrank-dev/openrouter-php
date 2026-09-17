<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class EmbeddingResponse
 *
 * Holds full embedding response payload and query metadata.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class EmbeddingResponse
{
    /**
     * EmbeddingResponse constructor.
     *
     * @param string|null $id
     * @param string $object
     * @param string $model
     * @param array<EmbeddingInfo> $data
     * @param array<string, int>|null $usage
     * @param ResponseMetadata $metadata
     */
    public function __construct(
        public ?string $id,
        public string $object,
        public string $model,
        public array $data,
        public ?array $usage,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build EmbeddingResponse from payload array and metadata.
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
                    $infos[] = EmbeddingInfo::fromArray($item);
                }
            }
        }

        $id = $data['id'] ?? null;
        $object = $data['object'] ?? null;
        $model = $data['model'] ?? null;

        return new self(
            id: is_string($id) ? $id : null,
            object: is_string($object) ? $object : '',
            model: is_string($model) ? $model : '',
            data: $infos,
            usage: isset($data['usage']) && is_array($data['usage']) ? $data['usage'] : null,
            metadata: $metadata ?? new ResponseMetadata()
        );
    }
}
