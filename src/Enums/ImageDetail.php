<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Enums;

/**
 * Enum ImageDetail
 *
 * Configures the resolution detail level of images in user content parts.
 *
 * @package MichaelFrank\OpenRouter\Enums
 */
enum ImageDetail: string
{
    case Auto = 'auto';
    case Low = 'low';
    case High = 'high';
}
