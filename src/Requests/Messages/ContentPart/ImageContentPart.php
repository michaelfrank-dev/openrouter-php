<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Messages\ContentPart;

use MichaelFrank\OpenRouter\Enums\ImageDetail;

/**
 * Class ImageContentPart
 *
 * Image segment for multimodal messages.
 *
 * @package MichaelFrank\OpenRouter\Requests\Messages\ContentPart
 */
final readonly class ImageContentPart implements ContentPart
{
    /**
     * ImageContentPart constructor.
     *
     * @param string $imageUrl
     * @param ImageDetail|null $detail
     */
    public function __construct(
        public string $imageUrl,
        public ?ImageDetail $detail = null,
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array{type: string, image_url: array{url: string, detail?: string}}
     */
    public function toArray(): array
    {
        $imageUrl = [
            'url' => $this->imageUrl,
        ];

        if ($this->detail !== null) {
            $imageUrl['detail'] = $this->detail->value;
        }

        return [
            'type' => 'image_url',
            'image_url' => $imageUrl,
        ];
    }

    /**
     * JSON serialization format.
     *
     * @return array{type: string, image_url: array{url: string, detail?: string}}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
