<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class ImageGenerationData
 *
 * Represents an individual generated image data block.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ImageGenerationData
{
    /**
     * ImageGenerationData constructor.
     *
     * @param string $b64Json Base64-encoded image bytes
     * @param string|null $mediaType Media type of the image
     */
    public function __construct(
        public string $b64Json,
        public ?string $mediaType = null,
    ) {
    }

    /**
     * Factory to build ImageGenerationData from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $b64Json = $data['b64_json'] ?? '';
        $mediaType = $data['media_type'] ?? null;

        return new self(
            b64Json: is_string($b64Json) ? $b64Json : '',
            mediaType: is_string($mediaType) ? $mediaType : null
        );
    }
}
