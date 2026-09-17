<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

/**
 * Class WebSearchEngine
 *
 * String-tolerant wrapper class for OpenRouter web search engine configurations.
 *
 * @see https://openrouter.ai/docs/guides/features/server-tools/web-search
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class WebSearchEngine
{
    public const AUTO = 'auto';
    public const NATIVE = 'native';
    public const EXA = 'exa';
    public const FIRECRAWL = 'firecrawl';
    public const PARALLEL = 'parallel';
    public const PERPLEXITY = 'perplexity';

    /**
     * WebSearchEngine constructor.
     *
     * @param string $value
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Creates an instance of WebSearchEngine from a raw string.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
