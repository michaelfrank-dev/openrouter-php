<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class ImageModelEndpointsResponse
 *
 * Holds the response from listing endpoints for an image model.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ImageModelEndpointsResponse
{
    /**
     * ImageModelEndpointsResponse constructor.
     *
     * @param string $id Model slug (e.g. 'bytedance-seed/seedream-4.5')
     * @param array<ImageEndpoint> $endpoints Endpoints serving the image model
     * @param ResponseMetadata $metadata Response metadata
     */
    public function __construct(
        public string $id,
        public array $endpoints,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build ImageModelEndpointsResponse from a payload array and metadata.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata|null $metadata
     * @return self
     */
    public static function fromArray(array $data, ?ResponseMetadata $metadata = null): self
    {
        $id = isset($data['id']) && is_string($data['id']) ? $data['id'] : '';
        $endpoints = [];
        if (isset($data['endpoints']) && is_array($data['endpoints'])) {
            foreach ($data['endpoints'] as $endpointData) {
                if (is_array($endpointData)) {
                    $endpoints[] = ImageEndpoint::fromArray($endpointData);
                }
            }
        }

        return new self(
            id: $id,
            endpoints: $endpoints,
            metadata: $metadata ?? new ResponseMetadata()
        );
    }
}
