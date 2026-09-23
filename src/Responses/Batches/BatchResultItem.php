<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Batches;

/**
 * Class BatchResultItem
 *
 * Represents an individual result entry within a completed OpenRouter batch.
 *
 * @package MichaelFrank\OpenRouter\Responses\Batches
 */
final readonly class BatchResultItem
{
    /**
     * BatchResultItem constructor.
     *
     * @param string $id Unique batch request item ID.
     * @param string $customId The custom ID supplied with the request item.
     * @param BatchResultResponse|null $response Successful response details, if available.
     * @param array<string, mixed>|null $error Error details, if the item failed.
     */
    public function __construct(
        public string $id,
        public string $customId,
        public ?BatchResultResponse $response = null,
        public ?array $error = null,
    ) {
    }

    /**
     * Instantiates BatchResultItem from array data.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $customId = $data['custom_id'] ?? null;

        return new self(
            id: is_string($id) ? $id : '',
            customId: is_string($customId) ? $customId : '',
            response: isset($data['response']) && is_array($data['response'])
                ? BatchResultResponse::fromArray($data['response'])
                : null,
            error: isset($data['error']) && is_array($data['error'])
                ? $data['error']
                : null,
        );
    }
}
