<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Exceptions;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class ApiException
 *
 * Abstract exception class representing errors returned by the OpenRouter API
 * or occurring during HTTP communication.
 *
 * @package MichaelFrank\OpenRouter\Exceptions
 */
abstract class ApiException extends \Exception implements OpenRouterException
{
    /**
     * ApiException constructor.
     *
     * @param string $message
     * @param ResponseMetadata|null $metadata
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = '',
        private readonly ?ResponseMetadata $metadata = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * Retrieves the response metadata associated with the exception.
     *
     * @return ResponseMetadata|null
     */
    public function getMetadata(): ?ResponseMetadata
    {
        return $this->metadata;
    }
}
