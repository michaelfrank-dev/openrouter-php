<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class ImageGenerationResponse
 *
 * Top-level response for image generation requests.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ImageGenerationResponse
{
    /**
     * ImageGenerationResponse constructor.
     *
     * @param int $created Unix timestamp of creation
     * @param array<ImageGenerationData> $data Generated images
     * @param Usage|null $usage Token and cost usage
     * @param ResponseMetadata $metadata Response headers/metadata
     */
    public function __construct(
        public int $created,
        public array $data,
        public ?Usage $usage,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build ImageGenerationResponse from payload array and metadata.
     *
     * @param array<string, mixed> $payload
     * @param ResponseMetadata $metadata
     * @return self
     */
    public static function fromArray(array $payload, ResponseMetadata $metadata): self
    {
        $created = $payload['created'] ?? 0;
        $createdVal = is_numeric($created) ? (int)$created : 0;

        $data = [];
        if (isset($payload['data']) && is_array($payload['data'])) {
            foreach ($payload['data'] as $item) {
                if (is_array($item)) {
                    $data[] = ImageGenerationData::fromArray($item);
                }
            }
        }

        $usage = null;
        if (isset($payload['usage']) && is_array($payload['usage'])) {
            $usage = Usage::fromArray($payload['usage']);
        }

        return new self(
            created: $createdVal,
            data: $data,
            usage: $usage,
            metadata: $metadata
        );
    }
}
