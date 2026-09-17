<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Enums;

/**
 * Enum ImageBackground
 *
 * Background treatment for image generation.
 *
 * @package MichaelFrank\OpenRouter\Enums
 */
enum ImageBackground: string
{
    case Auto = 'auto';
    case Transparent = 'transparent';
    case Opaque = 'opaque';
}
