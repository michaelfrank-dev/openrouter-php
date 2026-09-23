<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Batches;

/**
 * Class BatchEndpoint
 *
 * String-tolerant wrapper for OpenRouter batch endpoint shapes.
 *
 * @package MichaelFrank\OpenRouter\Requests\Batches
 * @see https://openrouter.ai/docs/guides/overview/batch-api
 */
final readonly class BatchEndpoint
{
    public const CHAT_COMPLETIONS = '/v1/chat/completions';
    public const RESPONSES = '/v1/responses';
    public const MESSAGES = '/v1/messages';
    public const EMBEDDINGS = '/v1/embeddings';

    /**
     * BatchEndpoint constructor.
     *
     * @param string $value
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Creates a BatchEndpoint instance from string.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
