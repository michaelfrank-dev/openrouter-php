<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Enums;

/**
 * Enum ImageQuality
 *
 * Rendering quality for image generation.
 *
 * @package MichaelFrank\OpenRouter\Enums
 */
enum ImageQuality: string
{
    case Auto = 'auto';
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
