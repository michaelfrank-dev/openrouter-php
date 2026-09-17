<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class RerankUsage
 *
 * Keeps track of rerank pricing cost and units metrics.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class RerankUsage
{
    /**
     * RerankUsage constructor.
     *
     * @param float|null $cost
     * @param int|null $searchUnits
     * @param int|null $totalTokens
     */
    public function __construct(
        public ?float $cost,
        public ?int $searchUnits,
        public ?int $totalTokens,
    ) {
    }

    /**
     * Factory to build RerankUsage from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $cost = $data['cost'] ?? null;
        $search = $data['search_units'] ?? null;
        $total = $data['total_tokens'] ?? null;

        return new self(
            cost: is_numeric($cost) ? (float)$cost : null,
            searchUnits: is_numeric($search) ? (int)$search : null,
            totalTokens: is_numeric($total) ? (int)$total : null
        );
    }
}
