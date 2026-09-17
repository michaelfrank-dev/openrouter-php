<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Streaming;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Metadata\RateLimit;

/**
 * Class CompletionStreamChunk
 *
 * Represents an individual chunk packet yielded by streaming response endpoints.
 *
 * @package MichaelFrank\OpenRouter\Responses\Streaming
 */
final readonly class CompletionStreamChunk
{
    /**
     * CompletionStreamChunk constructor.
     *
     * @param string|null $id
     * @param string|null $object
     * @param int|null $created
     * @param string|null $model
     * @param array<CompletionChoiceChunk> $choices
     * @param ResponseMetadata $metadata
     */
    public function __construct(
        public ?string $id,
        public ?string $object,
        public ?int $created,
        public ?string $model,
        public array $choices,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build CompletionStreamChunk from payload array.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata|null $metadata
     * @return self
     */
    public static function fromArray(array $data, ?ResponseMetadata $metadata = null): self
    {
        $choices = [];
        if (isset($data['choices']) && is_array($data['choices'])) {
            foreach ($data['choices'] as $choice) {
                if (is_array($choice)) {
                    $choices[] = CompletionChoiceChunk::fromArray($choice);
                }
            }
        }

        $id = $data['id'] ?? null;
        $object = $data['object'] ?? null;
        $created = $data['created'] ?? null;
        $model = $data['model'] ?? null;

        return new self(
            id: is_string($id) ? $id : null,
            object: is_string($object) ? $object : null,
            created: is_numeric($created) ? (int)$created : null,
            model: is_string($model) ? $model : null,
            choices: $choices,
            metadata: $metadata ?? new ResponseMetadata()
        );
    }
}
