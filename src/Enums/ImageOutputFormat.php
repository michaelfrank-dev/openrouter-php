<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Enums;

/**
 * Enum ImageOutputFormat
 *
 * Encoding format for image generation.
 *
 * @package MichaelFrank\OpenRouter\Enums
 */
enum ImageOutputFormat: string
{
    case Png = 'png';
    case Jpeg = 'jpeg';
    case Webp = 'webp';
    case Svg = 'svg';
}
