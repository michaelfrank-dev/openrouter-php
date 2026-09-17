<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class EmbeddingInfo
 *
 * Details of a single input embedding vector result.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class EmbeddingInfo
{
    /**
     * EmbeddingInfo constructor.
     *
     * @param string $object
     * @param int $index
     * @param string|array<float> $embedding
     */
    public function __construct(
        public string $object,
        public int $index,
        public string|array $embedding,
    ) {
    }

    /**
     * Factory to build EmbeddingInfo from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $object = $data['object'] ?? null;
        $index = $data['index'] ?? null;
        $embedding = $data['embedding'] ?? null;

        /** @var string|array<float> $embedVal */
        $embedVal = is_string($embedding) || is_array($embedding) ? $embedding : [];

        return new self(
            object: is_string($object) ? $object : '',
            index: is_numeric($index) ? (int)$index : 0,
            embedding: $embedVal
        );
    }
}
