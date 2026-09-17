<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Exceptions;

/**
 * Class NetworkException
 *
 * Thrown when a network-level issue prevents request transmission or response reception.
 * Implements Psr\Http\Client\ClientExceptionInterface for standard client compliance.
 *
 * @package MichaelFrank\OpenRouter\Exceptions
 */
final class NetworkException extends ApiException implements \Psr\Http\Client\ClientExceptionInterface
{
}
