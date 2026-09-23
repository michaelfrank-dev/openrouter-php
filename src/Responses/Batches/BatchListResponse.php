<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Batches;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class BatchListResponse
 *
 * Represents a paginated list of OpenRouter batches.
 *
 * @package MichaelFrank\OpenRouter\Responses\Batches
 * @see https://openrouter.ai/docs/guides/overview/batch-api#list-your-batches
 */
final readonly class BatchListResponse
{
    /**
     * BatchListResponse constructor.
     *
     * @param string $object Object type ('list').
     * @param array<int, BatchResponse> $data List of batch objects.
     * @param string|null $firstId The ID of the first batch in the list.
     * @param string|null $lastId The ID of the last batch in the list.
     * @param bool $hasMore Whether more batches are available after this page.
     * @param ResponseMetadata $metadata HTTP response metadata.
     */
    public function __construct(
        public string $object,
        public array $data,
        public ?string $firstId,
        public ?string $lastId,
        public bool $hasMore,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Instantiates BatchListResponse from API payload array and metadata.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata $metadata
     * @return self
     */
    public static function fromArray(array $data, ResponseMetadata $metadata): self
    {
        $batches = [];
        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $item) {
                if (is_array($item)) {
                    $batches[] = BatchResponse::fromArray($item, $metadata);
                }
            }
        }

        $object = $data['object'] ?? null;
        $firstId = $data['first_id'] ?? null;
        $lastId = $data['last_id'] ?? null;

        return new self(
            object: is_string($object) ? $object : 'list',
            data: $batches,
            firstId: is_string($firstId) ? $firstId : null,
            lastId: is_string($lastId) ? $lastId : null,
            hasMore: (bool) ($data['has_more'] ?? false),
            metadata: $metadata,
        );
    }
}
