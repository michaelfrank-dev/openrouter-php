<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Enums;

/**
 * Enum ToolType
 *
 * Represents the type of tool, typically function.
 *
 * @package MichaelFrank\OpenRouter\Enums
 */
enum ToolType: string
{
    case Function = 'function';
    case Unknown = 'unknown';

    /**
     * Safely maps a string value to a ToolType case, defaulting to Unknown.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::Unknown;
    }
}
