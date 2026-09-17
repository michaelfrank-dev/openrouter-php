<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Enums;

/**
 * Enum MessageRole
 *
 * Represents the role of a message author in the conversation.
 *
 * @package MichaelFrank\OpenRouter\Enums
 */
enum MessageRole: string
{
    case System = 'system';
    case User = 'user';
    case Assistant = 'assistant';
    case Tool = 'tool';
    case Unknown = 'unknown';

    /**
     * Safely maps a string value to a MessageRole case, defaulting to Unknown.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::Unknown;
    }
}
