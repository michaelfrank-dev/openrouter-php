<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Enums;

/**
 * Enum FinishReason
 *
 * Represents the reason the model stopped generating tokens.
 *
 * @package MichaelFrank\OpenRouter\Enums
 */
enum FinishReason: string
{
    case Stop = 'stop';
    case Length = 'length';
    case ContentFilter = 'content_filter';
    case ToolCalls = 'tool_calls';
    case FunctionCall = 'function_call';
    case Unknown = 'unknown';

    /**
     * Safely maps a string value to a FinishReason case, defaulting to Unknown.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::Unknown;
    }
}
