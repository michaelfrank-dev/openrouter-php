<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Messages\ContentPart;

/**
 * Interface ContentPart
 *
 * Represents a segment of content inside a user prompt (multimodal).
 *
 * @package MichaelFrank\OpenRouter\Requests\Messages\ContentPart
 */
interface ContentPart extends \JsonSerializable
{
    /**
     * Converts the content part to a serializable array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
