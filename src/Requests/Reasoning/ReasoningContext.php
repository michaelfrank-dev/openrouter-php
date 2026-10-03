<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Reasoning;

/**
 * Class ReasoningContext
 *
 * Extensible wrapper representing reasoning context mode across conversation turns.
 *
 * @see https://openrouter.ai/docs/guides/reasoning-tokens#reasoning-context
 * @package MichaelFrank\OpenRouter\Requests\Reasoning
 */
final readonly class ReasoningContext
{
    public const AUTO = 'auto';
    public const ALL_TURNS = 'all_turns';
    public const CURRENT_TURN = 'current_turn';

    /**
     * ReasoningContext constructor.
     *
     * @param string $value Reasoning context mode value.
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Create an instance from string value.
     *
     * @param string $value Reasoning context mode value.
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
