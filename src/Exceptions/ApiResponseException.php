<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Exceptions;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class ApiResponseException
 *
 * Thrown when OpenRouter returns a status code 200, but the response body
 * indicates an error or is syntactically invalid (e.g. malformed JSON).
 *
 * @package MichaelFrank\OpenRouter\Exceptions
 */
final class ApiResponseException extends ApiException
{
    /**
     * ApiResponseException constructor.
     *
     * @param string $message
     * @param string $responseBody
     * @param ResponseMetadata|null $metadata
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message,
        private readonly string $responseBody,
        ?ResponseMetadata $metadata = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $metadata, $previous);
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
