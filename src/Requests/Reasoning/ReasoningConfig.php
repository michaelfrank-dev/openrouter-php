<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Reasoning;

use JsonSerializable;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;

/**
 * Class ReasoningConfig
 *
 * Configures reasoning / thinking tokens for models supporting OpenRouter's unified reasoning parameter.
 *
 * @see https://openrouter.ai/docs/guides/reasoning-tokens
 * @package MichaelFrank\OpenRouter\Requests\Reasoning
 */
final readonly class ReasoningConfig implements JsonSerializable
{
    public ?ReasoningEffort $effort;
    public ?int $maxTokens;
    public ?bool $exclude;
    public ?bool $enabled;
    public ?ReasoningContext $context;
    public ?ReasoningMode $mode;

    /**
     * ReasoningConfig constructor.
     *
     * @param ReasoningEffort|string|null $effort Reasoning effort level (mutually exclusive with maxTokens).
     * @param int|null $maxTokens Maximum tokens allocated for reasoning (mutually exclusive with effort).
     * @param bool|null $exclude Whether to exclude reasoning tokens from response message.
     * @param bool|null $enabled Whether to enable reasoning with default parameters.
     * @param ReasoningContext|string|null $context Reasoning context mode across conversation turns.
     * @param ReasoningMode|string|null $mode Reasoning mode (e.g. pro mode).
     * @throws ValidationException When both effort and maxTokens are provided or maxTokens is negative.
     */
    public function __construct(
        ReasoningEffort|string|null $effort = null,
        ?int $maxTokens = null,
        ?bool $exclude = null,
        ?bool $enabled = null,
        ReasoningContext|string|null $context = null,
        ReasoningMode|string|null $mode = null,
    ) {
        if ($effort !== null && $maxTokens !== null) {
            throw new ValidationException('Cannot specify both "effort" and "maxTokens" for reasoning.');
        }

        if ($maxTokens !== null && $maxTokens < 0) {
            throw new ValidationException('Reasoning maxTokens must be non-negative.');
        }

        $this->effort = is_string($effort) ? ReasoningEffort::fromString($effort) : $effort;
        $this->maxTokens = $maxTokens;
        $this->exclude = $exclude;
        $this->enabled = $enabled;
        $this->context = is_string($context) ? ReasoningContext::fromString($context) : $context;
        $this->mode = is_string($mode) ? ReasoningMode::fromString($mode) : $mode;
    }

    /**
     * Named constructor to configure effort-based reasoning.
     *
     * @param ReasoningEffort|string $effort
     * @param bool|null $exclude
     * @param ReasoningContext|string|null $context
     * @param ReasoningMode|string|null $mode
     * @return self
     */
    public static function withEffort(
        ReasoningEffort|string $effort,
        ?bool $exclude = null,
        ReasoningContext|string|null $context = null,
        ReasoningMode|string|null $mode = null,
    ): self {
        return new self(
            effort: $effort,
            exclude: $exclude,
            context: $context,
            mode: $mode
        );
    }

    /**
     * Named constructor to configure token-budget reasoning.
     *
     * @param int $maxTokens
     * @param bool|null $exclude
     * @param ReasoningContext|string|null $context
     * @param ReasoningMode|string|null $mode
     * @return self
     */
    public static function withMaxTokens(
        int $maxTokens,
        ?bool $exclude = null,
        ReasoningContext|string|null $context = null,
        ReasoningMode|string|null $mode = null,
    ): self {
        return new self(
            maxTokens: $maxTokens,
            exclude: $exclude,
            context: $context,
            mode: $mode
        );
    }

    /**
     * Named constructor to explicitly disable reasoning.
     *
     * @return self
     */
    public static function disabled(): self
    {
        return new self(enabled: false);
    }

    /**
     * Named constructor to enable reasoning with default parameters.
     *
     * @return self
     */
    public static function default(): self
    {
        return new self(enabled: true);
    }

    /**
     * Ingest options from associative array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $effort = $data['effort'] ?? null;
        $maxTokens = $data['max_tokens'] ?? $data['maxTokens'] ?? null;
        $exclude = $data['exclude'] ?? null;
        $enabled = $data['enabled'] ?? null;
        $context = $data['context'] ?? null;
        $mode = $data['mode'] ?? null;

        return new self(
            effort: is_string($effort) ? $effort : null,
            maxTokens: is_numeric($maxTokens) ? (int)$maxTokens : null,
            exclude: is_bool($exclude) ? $exclude : null,
            enabled: is_bool($enabled) ? $enabled : null,
            context: is_string($context) ? $context : null,
            mode: is_string($mode) ? $mode : null
        );
    }

    /**
     * Converts to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->effort !== null) {
            $data['effort'] = $this->effort->value;
        }

        if ($this->maxTokens !== null) {
            $data['max_tokens'] = $this->maxTokens;
        }

        if ($this->exclude !== null) {
            $data['exclude'] = $this->exclude;
        }

        if ($this->enabled !== null) {
            $data['enabled'] = $this->enabled;
        }

        if ($this->context !== null) {
            $data['context'] = $this->context->value;
        }

        if ($this->mode !== null) {
            $data['mode'] = $this->mode->value;
        }

        return $data;
    }

    /**
     * JSON serialization format.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
