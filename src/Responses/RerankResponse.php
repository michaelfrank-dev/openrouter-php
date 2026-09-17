<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class RerankResponse
 *
 * Holds full reranking results and usage metrics.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class RerankResponse
{
    /**
     * RerankResponse constructor.
     *
     * @param string|null $id
     * @param string $model
     * @param string|null $provider
     * @param array<RerankResult> $results
     * @param RerankUsage|null $usage
     * @param ResponseMetadata $metadata
     */
    public function __construct(
        public ?string $id,
        public string $model,
        public ?string $provider,
        public array $results,
        public ?RerankUsage $usage,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build RerankResponse from payload array and metadata.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata|null $metadata
     * @return self
     */
    public static function fromArray(array $data, ?ResponseMetadata $metadata = null): self
    {
        $results = [];
        if (isset($data['results']) && is_array($data['results'])) {
            foreach ($data['results'] as $res) {
                if (is_array($res)) {
                    $results[] = RerankResult::fromArray($res);
                }
            }
        }

        $id = $data['id'] ?? null;
        $model = $data['model'] ?? null;
        $provider = $data['provider'] ?? null;

        return new self(
            id: is_string($id) ? $id : null,
            model: is_string($model) ? $model : '',
            provider: is_string($provider) ? $provider : null,
            results: $results,
            usage: isset($data['usage']) && is_array($data['usage']) ? RerankUsage::fromArray($data['usage']) : null,
            metadata: $metadata ?? new ResponseMetadata()
        );
    }
}
