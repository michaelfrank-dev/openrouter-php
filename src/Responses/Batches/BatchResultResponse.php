<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Batches;

/**
 * Class BatchResultResponse
 *
 * Represents the HTTP response payload for an individual batch request item.
 *
 * @package MichaelFrank\OpenRouter\Responses\Batches
 */
final readonly class BatchResultResponse
{
    /**
     * BatchResultResponse constructor.
     *
     * @param int $statusCode HTTP status code returned for this request item.
     * @param string|null $requestId Unique request identifier.
     * @param array<string, mixed> $body Response body payload (e.g., chat completion or embedding output).
     */
    public function __construct(
        public int $statusCode,
        public ?string $requestId,
        public array $body,
    ) {
    }

    /**
     * Instantiates BatchResultResponse from array data.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $statusCode = $data['status_code'] ?? null;
        $requestId = $data['request_id'] ?? null;

        return new self(
            statusCode: is_numeric($statusCode) ? (int) $statusCode : 200,
            requestId: is_string($requestId) ? $requestId : null,
            body: is_array($data['body'] ?? null) ? $data['body'] : [],
        );
    }
}
