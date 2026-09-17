<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class CreditsResponse
 *
 * Holds token credit and usage balances. All numerical fields are nullable.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class CreditsResponse
{
    /**
     * CreditsResponse constructor.
     *
     * @param float|null $creditsPurchased
     * @param float|null $creditsUsed
     * @param float|null $creditsRemaining
     * @param ResponseMetadata $metadata
     */
    public function __construct(
        public ?float $creditsPurchased,
        public ?float $creditsUsed,
        public ?float $creditsRemaining,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build CreditsResponse from payload array and metadata.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata|null $metadata
     * @return self
     */
    public static function fromArray(array $data, ?ResponseMetadata $metadata = null): self
    {
        // Extract keys from response body data
        $nestedData = isset($data['data']) && is_array($data['data']) ? $data['data'] : $data;

        $purchased = null;
        if (isset($nestedData['credits_purchased'])) {
            $purchased = (float)$nestedData['credits_purchased'];
        } elseif (isset($nestedData['total_credits'])) {
            $purchased = (float)$nestedData['total_credits'];
        } elseif (isset($nestedData['total_limit'])) {
            $purchased = (float)$nestedData['total_limit'];
        } elseif (isset($nestedData['limit'])) {
            $purchased = (float)$nestedData['limit'];
        }

        $used = null;
        if (isset($nestedData['credits_used'])) {
            $used = (float)$nestedData['credits_used'];
        } elseif (isset($nestedData['total_usage'])) {
            $used = (float)$nestedData['total_usage'];
        } elseif (isset($nestedData['usage'])) {
            $used = (float)$nestedData['usage'];
        }

        $remaining = null;
        if (isset($nestedData['credits_remaining'])) {
            $remaining = (float)$nestedData['credits_remaining'];
        } elseif (isset($nestedData['total_remaining'])) {
            $remaining = (float)$nestedData['total_remaining'];
        } elseif (isset($nestedData['limit_remaining'])) {
            $remaining = (float)$nestedData['limit_remaining'];
        } elseif ($purchased !== null && $used !== null) {
            $remaining = $purchased - $used;
        }

        return new self(
            creditsPurchased: $purchased,
            creditsUsed: $used,
            creditsRemaining: $remaining,
            metadata: $metadata ?? new ResponseMetadata()
        );
    }
}
