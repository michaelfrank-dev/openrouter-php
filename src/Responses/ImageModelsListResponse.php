<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class ImageModelsListResponse
 *
 * Holds the response from listing image generation models.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ImageModelsListResponse
{
    /**
     * ImageModelsListResponse constructor.
     *
     * @param array<ImageModelListItem> $data List of image generation models
     * @param ResponseMetadata $metadata Response metadata
     */
    public function __construct(
        public array $data,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build ImageModelsListResponse from a payload array and metadata.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata|null $metadata
     * @return self
     */
    public static function fromArray(array $data, ?ResponseMetadata $metadata = null): self
    {
        $items = [];
        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $item) {
                if (is_array($item)) {
                    $items[] = ImageModelListItem::fromArray($item);
                }
            }
        }

        return new self(
            data: $items,
            metadata: $metadata ?? new ResponseMetadata()
        );
    }
}
