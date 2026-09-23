<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Enums;

/**
 * Enum BatchStatus
 *
 * Represents the lifecycle status of an asynchronous OpenRouter batch.
 *
 * @package MichaelFrank\OpenRouter\Enums
 */
enum BatchStatus: string
{
    case Validating = 'validating';
    case InProgress = 'in_progress';
    case Finalizing = 'finalizing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Expired = 'expired';
    case Cancelling = 'cancelling';
    case Cancelled = 'cancelled';
    case Unknown = 'unknown';

    /**
     * Safely maps a string value to a BatchStatus case, defaulting to Unknown.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::Unknown;
    }
}
