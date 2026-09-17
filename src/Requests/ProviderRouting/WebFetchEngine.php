<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

/**
 * Class WebFetchEngine
 *
 * String-tolerant wrapper class for OpenRouter web fetch engine configurations.
 *
 * @see https://openrouter.ai/blog/agentic-web-tools/
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class WebFetchEngine
{
    public const AUTO = 'auto';

    /**
     * WebFetchEngine constructor.
     *
     * @param string $value
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Creates an instance of WebFetchEngine from a raw string.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
