<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Streaming;

use MichaelFrank\OpenRouter\Responses\Usage;

/**
 * Class ImageGenCompletedEvent
 *
 * Emitted when generation completes and the final image is available.
 *
 * @package MichaelFrank\OpenRouter\Responses\Streaming
 */
final readonly class ImageGenCompletedEvent implements ImageStreamEvent
{
    /**
     * ImageGenCompletedEvent constructor.
     *
     * @param string $b64Json Base64-encoded final image data
     * @param int $created Unix timestamp of creation
     * @param string|null $mediaType Media type of the image
     * @param Usage|null $usage Token and cost usage
     * @param string $type The event type
     */
    public function __construct(
        public string $b64Json,
        public int $created,
        public ?string $mediaType = null,
        public ?Usage $usage = null,
        public string $type = 'image_generation.completed',
    ) {
    }

    /**
     * Gets the event type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Factory to build the event from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $usage = null;
        if (isset($data['usage']) && is_array($data['usage'])) {
            $usage = Usage::fromArray($data['usage']);
        }

        $b64Json = $data['b64_json'] ?? '';
        $created = $data['created'] ?? 0;
        $type = $data['type'] ?? 'image_generation.completed';

        return new self(
            b64Json: is_string($b64Json) ? $b64Json : '',
            created: is_numeric($created) ? (int)$created : 0,
            mediaType: is_string($data['media_type'] ?? null) ? $data['media_type'] : null,
            usage: $usage,
            type: is_string($type) ? $type : 'image_generation.completed'
        );
    }
}
