<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Exceptions;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class ApiRequestException
 *
 * Thrown when OpenRouter returns a status code >= 400.
 *
 * @package MichaelFrank\OpenRouter\Exceptions
 */
final class ApiRequestException extends ApiException
{
    /**
     * ApiRequestException constructor.
     *
     * @param string $message
     * @param int $statusCode
     * @param string $responseBody
     * @param ResponseMetadata|null $metadata
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message,
        private readonly int $statusCode,
        private readonly string $responseBody,
        ?ResponseMetadata $metadata = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $metadata, $previous);
    }

    /**
     * Gets the HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Gets the raw response body.
     *
     * @return string
     */
    public function getResponseBody(): string
    {
        return $this->responseBody;
    }
}
