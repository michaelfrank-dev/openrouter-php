<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Batches;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class BatchDeletionResponse
 *
 * Represents the response from deleting an OpenRouter batch.
 *
 * @package MichaelFrank\OpenRouter\Responses\Batches
 * @see https://openrouter.ai/docs/guides/overview/batch-api#delete-a-batch
 */
final readonly class BatchDeletionResponse
{
    /**
     * BatchDeletionResponse constructor.
     *
     * @param string $id ID of the deleted batch.
     * @param string $object Object type ('batch').
     * @param BatchDeletionDetails $deletion Deletion outcome details per target.
     * @param ResponseMetadata $metadata HTTP response metadata.
     */
    public function __construct(
        public string $id,
        public string $object,
        public BatchDeletionDetails $deletion,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Instantiates BatchDeletionResponse from API payload array and metadata.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata $metadata
     * @return self
     */
    public static function fromArray(array $data, ResponseMetadata $metadata): self
    {
        $deletionData = isset($data['deletion']) && is_array($data['deletion']) ? $data['deletion'] : [];
        $id = $data['id'] ?? null;
        $object = $data['object'] ?? null;

        return new self(
            id: is_string($id) ? $id : '',
            object: is_string($object) ? $object : 'batch',
            deletion: BatchDeletionDetails::fromArray($deletionData),
            metadata: $metadata,
        );
    }
}
