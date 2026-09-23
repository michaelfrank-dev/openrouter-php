<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Batches;

/**
 * Class BatchRequestCounts
 *
 * Request progress and completion counts for an OpenRouter batch.
 *
 * @package MichaelFrank\OpenRouter\Responses\Batches
 */
final readonly class BatchRequestCounts
{
    /**
     * BatchRequestCounts constructor.
     *
     * @param int $total Total number of requests submitted in the batch.
     * @param int $completed Number of successfully processed requests.
     * @param int $failed Number of failed requests.
     */
    public function __construct(
        public int $total,
        public int $completed,
        public int $failed,
    ) {
    }

    /**
     * Instantiates BatchRequestCounts from API response array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $total = $data['total'] ?? null;
        $completed = $data['completed'] ?? null;
        $failed = $data['failed'] ?? null;

        return new self(
            total: is_numeric($total) ? (int) $total : 0,
            completed: is_numeric($completed) ? (int) $completed : 0,
            failed: is_numeric($failed) ? (int) $failed : 0,
        );
    }
}
