<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Batches;

/**
 * Class BatchDeletionDetails
 *
 * Details of batch deletion across OpenRouter and upstream provider targets.
 *
 * @package MichaelFrank\OpenRouter\Responses\Batches
 */
final readonly class BatchDeletionDetails
{
    /**
     * BatchDeletionDetails constructor.
     *
     * @param string $openrouter OpenRouter deletion status (e.g. 'deleted').
     * @param BatchUpstreamDeletionDetails|null $upstream Upstream deletion details, if provider was assigned.
     */
    public function __construct(
        public string $openrouter,
        public ?BatchUpstreamDeletionDetails $upstream = null,
    ) {
    }

    /**
     * Instantiates BatchDeletionDetails from array data.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $openrouter = $data['openrouter'] ?? null;

        return new self(
            openrouter: is_string($openrouter) ? $openrouter : 'deleted',
            upstream: isset($data['upstream']) && is_array($data['upstream'])
                ? BatchUpstreamDeletionDetails::fromArray($data['upstream'])
                : null,
        );
    }
}
