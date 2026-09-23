<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Batches;

use MichaelFrank\OpenRouter\Exceptions\ValidationException;

/**
 * Class BatchProviderRouting
 *
 * Configures provider routing constraints specifically for batch submissions.
 * Only 'only' is accepted by the Batch API.
 *
 * @package MichaelFrank\OpenRouter\Requests\Batches
 * @see https://openrouter.ai/docs/guides/overview/batch-api#provider-routing
 */
final readonly class BatchProviderRouting implements \JsonSerializable
{
    /**
     * BatchProviderRouting constructor.
     *
     * @param array<int, string> $only List of provider slugs to pin the batch to.
     * @throws ValidationException
     */
    public function __construct(
        public array $only
    ) {
        if (empty($this->only)) {
            throw new ValidationException('Batch provider routing "only" list cannot be empty.');
        }

        foreach ($this->only as $slug) {
            if (trim($slug) === '') {
                throw new ValidationException('Provider slug in "only" list must be a non-empty string.');
            }
        }
    }

    /**
     * Converts to array representation.
     *
     * @return array{only: array<int, string>}
     */
    public function toArray(): array
    {
        return [
            'only' => array_values($this->only),
        ];
    }

    /**
     * Serializes to JSON.
     *
     * @return array{only: array<int, string>}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
