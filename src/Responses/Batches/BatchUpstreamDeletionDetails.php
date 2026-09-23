<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Batches;

/**
 * Class BatchUpstreamDeletionDetails
 *
 * Upstream provider batch deletion status.
 *
 * @package MichaelFrank\OpenRouter\Responses\Batches
 */
final readonly class BatchUpstreamDeletionDetails
{
    /**
     * BatchUpstreamDeletionDetails constructor.
     *
     * @param string $provider Upstream provider name/slug.
     * @param string $status Upstream deletion status (e.g. 'deleted', 'unsupported', 'not_applicable').
     */
    public function __construct(
        public string $provider,
        public string $status,
    ) {
    }

    /**
     * Instantiates BatchUpstreamDeletionDetails from array data.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $provider = $data['provider'] ?? null;
        $status = $data['status'] ?? null;

        return new self(
            provider: is_string($provider) ? $provider : '',
            status: is_string($status) ? $status : '',
        );
    }
}
