<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class RerankResult
 *
 * Holds reranking context details for a single document.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class RerankResult
{
    /**
     * RerankResult constructor.
     *
     * @param array{text?: string, image?: string} $document
     * @param int $index
     * @param float $relevanceScore
     */
    public function __construct(
        public array $document,
        public int $index,
        public float $relevanceScore,
    ) {
    }

    /**
     * Factory to build RerankResult from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $doc = $data['document'] ?? null;
        $index = $data['index'] ?? null;
        $score = $data['relevance_score'] ?? null;

        /** @var array{text?: string, image?: string} $documentVal */
        $documentVal = is_array($doc) ? $doc : [];

        return new self(
            document: $documentVal,
            index: is_numeric($index) ? (int)$index : 0,
            relevanceScore: is_numeric($score) ? (float)$score : 0.0
        );
    }
}
