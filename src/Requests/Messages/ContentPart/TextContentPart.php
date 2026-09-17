<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Messages\ContentPart;

/**
 * Class TextContentPart
 *
 * Plain text segment for multimodal messages.
 *
 * @package MichaelFrank\OpenRouter\Requests\Messages\ContentPart
 */
final readonly class TextContentPart implements ContentPart
{
    /**
     * TextContentPart constructor.
     *
     * @param string $text
     */
    public function __construct(public string $text)
    {
    }

    /**
     * Converts to payload array.
     *
     * @return array{type: string, text: string}
     */
    public function toArray(): array
    {
        return [
            'type' => 'text',
            'text' => $this->text,
        ];
    }

    /**
     * JSON serialization format.
     *
     * @return array{type: string, text: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
