<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Exceptions;

/**
 * Class ConfigurationException
 *
 * Thrown when client configuration is invalid (e.g. non-HTTPS base URI)
 * or PSR-17/18 auto-discovery fails.
 *
 * @package MichaelFrank\OpenRouter\Exceptions
 */
final class ConfigurationException extends ApiException
{
}
