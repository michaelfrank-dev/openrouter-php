<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Responses\TokenDetails\CompletionTokensDetails;
use MichaelFrank\OpenRouter\Responses\TokenDetails\PromptTokensDetails;

/**
 * Class Usage
 *
 * Detailed consumption statistics of the request.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class Usage
{
    /**
     * Usage constructor.
     *
     * @param int $promptTokens
     * @param int $completionTokens
     * @param int $totalTokens
     * @param PromptTokensDetails|null $promptTokensDetails
     * @param CompletionTokensDetails|null $completionTokensDetails
     * @param float|null $cost
     * @param bool|null $isByok
     * @param UsageCostDetails|null $costDetails
     * @param ServerToolUse|null $serverToolUse
     * @param string|null $serviceTier
     */
    public function __construct(
        public int $promptTokens,
        public int $completionTokens,
        public int $totalTokens,
        public ?PromptTokensDetails $promptTokensDetails,
        public ?CompletionTokensDetails $completionTokensDetails,
        public ?float $cost,
        public ?bool $isByok,
        public ?UsageCostDetails $costDetails,
        public ?ServerToolUse $serverToolUse,
        public ?string $serviceTier = null,
    ) {
    }

    /**
     * Factory to build Usage from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $prompt = $data['prompt_tokens'] ?? null;
        $completion = $data['completion_tokens'] ?? null;
        $total = $data['total_tokens'] ?? null;
        $cost = $data['cost'] ?? null;
        $serviceTier = $data['service_tier'] ?? null;

        return new self(
            promptTokens: is_numeric($prompt) ? (int)$prompt : 0,
            completionTokens: is_numeric($completion) ? (int)$completion : 0,
            totalTokens: is_numeric($total) ? (int)$total : 0,
            promptTokensDetails: isset($data['prompt_tokens_details']) && is_array($data['prompt_tokens_details'])
                ? PromptTokensDetails::fromArray($data['prompt_tokens_details'])
                : null,
            completionTokensDetails: isset($data['completion_tokens_details']) && is_array($data['completion_tokens_details'])
                ? CompletionTokensDetails::fromArray($data['completion_tokens_details'])
                : null,
            cost: is_numeric($cost) ? (float)$cost : null,
            isByok: isset($data['is_byok']) ? (bool)$data['is_byok'] : null,
            costDetails: isset($data['cost_details']) && is_array($data['cost_details'])
                ? UsageCostDetails::fromArray($data['cost_details'])
                : null,
            serverToolUse: isset($data['server_tool_use']) && is_array($data['server_tool_use'])
                ? ServerToolUse::fromArray($data['server_tool_use'])
                : null,
            serviceTier: is_string($serviceTier) ? $serviceTier : null
        );
    }
}
