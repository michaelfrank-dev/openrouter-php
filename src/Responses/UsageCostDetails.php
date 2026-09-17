<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class UsageCostDetails
 *
 * Cost components of model inference, specifically for custom/upstream routing.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class UsageCostDetails
{
    /**
     * UsageCostDetails constructor.
     *
     * @param float|null $upstreamInferenceCost
     * @param float|null $upstreamInferencePromptCost
     * @param float|null $upstreamInferenceCompletionsCost
     */
    public function __construct(
        public ?float $upstreamInferenceCost,
        public ?float $upstreamInferencePromptCost,
        public ?float $upstreamInferenceCompletionsCost,
    ) {
    }

    /**
     * Factory to build UsageCostDetails from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $cost = $data['upstream_inference_cost'] ?? null;
        $prompt = $data['upstream_inference_prompt_cost'] ?? null;
        $completion = $data['upstream_inference_completions_cost'] ?? null;

        return new self(
            upstreamInferenceCost: is_numeric($cost) ? (float)$cost : null,
            upstreamInferencePromptCost: is_numeric($prompt) ? (float)$prompt : null,
            upstreamInferenceCompletionsCost: is_numeric($completion) ? (float)$completion : null
        );
    }
}
