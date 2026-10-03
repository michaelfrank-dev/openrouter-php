<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Reasoning;

/**
 * Class ReasoningMode
 *
 * Extensible wrapper representing reasoning mode (e.g. pro mode selection).
 *
 * @see https://openrouter.ai/docs/guides/reasoning-tokens#reasoning-mode
 * @package MichaelFrank\OpenRouter\Requests\Reasoning
 */
final readonly class ReasoningMode
{
    public const STANDARD = 'standard';
    public const PRO = 'pro';

    /**
     * ReasoningMode constructor.
     *
     * @param string $value Reasoning mode value.
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Create an instance from string value.
     *
     * @param string $value Reasoning mode value.
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
