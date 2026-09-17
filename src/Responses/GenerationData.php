<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class GenerationData
 *
 * Stats metrics context details for a single model generation run.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class GenerationData
{
    /**
     * GenerationData constructor.
     *
     * @param string|null $id
     * @param string|null $model
     * @param string|null $provider
     * @param float|null $price
     * @param float|null $cacheDiscount
     * @param int|null $tokens
     * @param int|null $tokensPrompt
     * @param int|null $tokensCompletion
     * @param int|null $nativeTokensPrompt
     * @param int|null $nativeTokensCompletion
     * @param int|null $nativeTokensReasoning
     * @param int|null $nativeTokensCached
     * @param float|null $latency
     * @param string|null $finishReason
     * @param \DateTimeImmutable|null $createdAt
     */
    public function __construct(
        public ?string $id,
        public ?string $model,
        public ?string $provider,
        public ?float $price,
        public ?float $cacheDiscount,
        public ?int $tokens,
        public ?int $tokensPrompt,
        public ?int $tokensCompletion,
        public ?int $nativeTokensPrompt,
        public ?int $nativeTokensCompletion,
        public ?int $nativeTokensReasoning,
        public ?int $nativeTokensCached,
        public ?float $latency,
        public ?string $finishReason,
        public ?\DateTimeImmutable $createdAt,
    ) {
    }

    /**
     * Factory to build GenerationData from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $model = $data['model'] ?? null;
        $provider = $data['provider_name'] ?? $data['provider'] ?? null;
        $price = $data['total_cost'] ?? $data['cost'] ?? $data['price'] ?? null;
        $discount = $data['cache_discount'] ?? null;
        $tokens = $data['tokens'] ?? $data['total_tokens'] ?? null;
        $prompt = $data['tokens_prompt'] ?? null;
        $completion = $data['tokens_completion'] ?? null;
        $nativePrompt = $data['native_tokens_prompt'] ?? null;
        $nativeCompletion = $data['native_tokens_completion'] ?? null;
        $nativeReasoning = $data['native_tokens_reasoning'] ?? null;
        $nativeCached = $data['native_tokens_cached'] ?? null;
        $latency = $data['latency'] ?? null;
        $finish = $data['finish_reason'] ?? null;

        $createdAt = null;
        if (isset($data['created_at']) && is_string($data['created_at'])) {
            try {
                $createdAt = new \DateTimeImmutable($data['created_at']);
            } catch (\Exception) {
                // Ignore date parsing failures
            }
        }

        return new self(
            id: is_string($id) ? $id : null,
            model: is_string($model) ? $model : null,
            provider: is_string($provider) ? $provider : null,
            price: is_numeric($price) ? (float)$price : null,
            cacheDiscount: is_numeric($discount) ? (float)$discount : null,
            tokens: is_numeric($tokens) ? (int)$tokens : null,
            tokensPrompt: is_numeric($prompt) ? (int)$prompt : null,
            tokensCompletion: is_numeric($completion) ? (int)$completion : null,
            nativeTokensPrompt: is_numeric($nativePrompt) ? (int)$nativePrompt : null,
            nativeTokensCompletion: is_numeric($nativeCompletion) ? (int)$nativeCompletion : null,
            nativeTokensReasoning: is_numeric($nativeReasoning) ? (int)$nativeReasoning : null,
            nativeTokensCached: is_numeric($nativeCached) ? (int)$nativeCached : null,
            latency: is_numeric($latency) ? (float)$latency : null,
            finishReason: is_string($finish) ? $finish : null,
            createdAt: $createdAt
        );
    }
}
