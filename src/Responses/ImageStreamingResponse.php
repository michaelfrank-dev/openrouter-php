<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageGenCompletedEvent;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageGenPartialImageEvent;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageGenStreamErrorEvent;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageGenTextChunkEvent;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageStreamEvent;

/**
 * Class ImageStreamingResponse
 *
 * Wraps streaming image event responses.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ImageStreamingResponse
{
    /**
     * ImageStreamingResponse constructor.
     *
     * @param ImageStreamEvent $data The event payload
     * @param ResponseMetadata $metadata Response metadata
     */
    public function __construct(
        public ImageStreamEvent $data,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build ImageStreamingResponse from payload array and metadata.
     *
     * @param array<string, mixed> $payload
     * @param ResponseMetadata $metadata
     * @return self
     */
    public static function fromArray(array $payload, ResponseMetadata $metadata): self
    {
        $eventData = $payload;
        if (isset($payload['data']) && is_array($payload['data'])) {
            $eventData = $payload['data'];
        }

        $type = $eventData['type'] ?? 'image_generation.partial_image';

        $event = match ($type) {
            'image_generation.text_chunk' => ImageGenTextChunkEvent::fromArray($eventData),
            'image_generation.completed' => ImageGenCompletedEvent::fromArray($eventData),
            'error' => ImageGenStreamErrorEvent::fromArray($eventData),
            default => ImageGenPartialImageEvent::fromArray($eventData),
        };

        return new self(
            data: $event,
            metadata: $metadata
        );
    }
}
