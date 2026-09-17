<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Messages;

use MichaelFrank\OpenRouter\Requests\Messages\ContentPart\ContentPart;

/**
 * Class UserMessage
 *
 * Represents a user role message, supporting simple text or multimodal parts.
 *
 * @package MichaelFrank\OpenRouter\Requests\Messages
 */
final readonly class UserMessage implements MessageInterface
{
    /**
     * UserMessage constructor.
     *
     * @param string|array<ContentPart> $content
     */
    public function __construct(public string|array $content)
    {
    }

    /**
     * Converts to payload array.
     *
     * @return array{role: string, content: string|array<array<string, mixed>>}
     */
    public function toArray(): array
    {
        $contentVal = $this->content;
        if (is_array($contentVal)) {
            $contentVal = array_map(
                static fn(ContentPart $part): array => $part->toArray(),
                $contentVal
            );
        }

        return [
            'role' => 'user',
            'content' => $contentVal,
        ];
    }

    /**
     * JSON serialization format.
     *
     * @return array{role: string, content: string|array<array<string, mixed>>}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
