<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Enums;

/**
 * Enum Verbosity
 *
 * Configures output detail level.
 *
 * @package MichaelFrank\OpenRouter\Enums
 */
enum Verbosity: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    /**
     * Safely maps a string value to a Verbosity case, or returns null if not matching.
     *
     * @param string $value
     * @return self|null
     */
    public static function fromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
