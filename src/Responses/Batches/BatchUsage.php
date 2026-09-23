<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Batches;

/**
 * Class BatchUsage
 *
 * Token consumption and billing details for a completed OpenRouter batch.
 *
 * @package MichaelFrank\OpenRouter\Responses\Batches
 */
final readonly class BatchUsage
{
    /**
     * BatchUsage constructor.
     *
     * @param int|null $promptTokens
     * @param int|null $completionTokens
     * @param int|null $totalTokens
     * @param float|null $cost
     * @param bool|null $isByok
     */
    public function __construct(
        public ?int $promptTokens = null,
        public ?int $completionTokens = null,
        public ?int $totalTokens = null,
        public ?float $cost = null,
        public ?bool $isByok = null,
    ) {
    }

    /**
     * Instantiates BatchUsage from API response array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $promptTokens = $data['prompt_tokens'] ?? null;
        $completionTokens = $data['completion_tokens'] ?? null;
        $totalTokens = $data['total_tokens'] ?? null;
        $cost = $data['cost'] ?? null;
        $isByok = $data['is_byok'] ?? null;

        return new self(
            promptTokens: is_numeric($promptTokens) ? (int) $promptTokens : null,
            completionTokens: is_numeric($completionTokens) ? (int) $completionTokens : null,
            totalTokens: is_numeric($totalTokens) ? (int) $totalTokens : null,
            cost: is_numeric($cost) ? (float) $cost : null,
            isByok: is_bool($isByok) ? $isByok : null,
        );
    }
}
