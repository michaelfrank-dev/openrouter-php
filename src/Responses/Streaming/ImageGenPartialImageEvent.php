<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Streaming;

/**
 * Class ImageGenPartialImageEvent
 *
 * Emitted when a partial image becomes available.
 *
 * @package MichaelFrank\OpenRouter\Responses\Streaming
 */
final readonly class ImageGenPartialImageEvent implements ImageStreamEvent
{
    /**
     * ImageGenPartialImageEvent constructor.
     *
     * @param string $b64Json Base64-encoded partial image data
     * @param int $partialImageIndex 0-based index indicating partial image sequence
     * @param string $type The event type
     */
    public function __construct(
        public string $b64Json,
        public int $partialImageIndex,
        public string $type = 'image_generation.partial_image',
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
        $b64Json = $data['b64_json'] ?? '';
        $index = $data['partial_image_index'] ?? 0;
        $type = $data['type'] ?? 'image_generation.partial_image';

        return new self(
            b64Json: is_string($b64Json) ? $b64Json : '',
            partialImageIndex: is_numeric($index) ? (int)$index : 0,
            type: is_string($type) ? $type : 'image_generation.partial_image'
        );
    }
}
