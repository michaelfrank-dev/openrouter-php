<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Streaming;

/**
 * Class ImageGenTextChunkEvent
 *
 * Emitted when a text chunk becomes available (e.g. SVG).
 *
 * @package MichaelFrank\OpenRouter\Responses\Streaming
 */
final readonly class ImageGenTextChunkEvent implements ImageStreamEvent
{
    /**
     * ImageGenTextChunkEvent constructor.
     *
     * @param string $text A text fragment of the image being generated
     * @param string $phase The generation phase (content, reasoning, draft)
     * @param string $type The event type
     */
    public function __construct(
        public string $text,
        public string $phase,
        public string $type = 'image_generation.text_chunk',
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
        $text = $data['text'] ?? '';
        $phase = $data['phase'] ?? 'content';
        $type = $data['type'] ?? 'image_generation.text_chunk';

        return new self(
            text: is_string($text) ? $text : '',
            phase: is_string($phase) ? $phase : 'content',
            type: is_string($type) ? $type : 'image_generation.text_chunk'
        );
    }
}
