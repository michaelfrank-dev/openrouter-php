<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Reasoning;

/**
 * Class ReasoningEffort
 *
 * Extensible wrapper representing reasoning effort level sent to the model.
 *
 * @see https://openrouter.ai/docs/guides/reasoning-tokens#reasoning-effort-level
 * @package MichaelFrank\OpenRouter\Requests\Reasoning
 */
final readonly class ReasoningEffort
{
    public const MAX = 'max';
    public const XHIGH = 'xhigh';
    public const HIGH = 'high';
    public const MEDIUM = 'medium';
    public const LOW = 'low';
    public const MINIMAL = 'minimal';
    public const NONE = 'none';

    /**
     * ReasoningEffort constructor.
     *
     * @param string $value Effort level string.
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Create an instance from string value.
     *
     * @param string $value Effort level string.
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
