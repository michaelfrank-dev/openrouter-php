<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Batches;

use MichaelFrank\OpenRouter\Enums\BatchStatus;
use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class BatchResponse
 *
 * Represents an OpenRouter batch object.
 *
 * @package MichaelFrank\OpenRouter\Responses\Batches
 * @see https://openrouter.ai/docs/guides/overview/batch-api
 */
final readonly class BatchResponse
{
    /**
     * BatchResponse constructor.
     *
     * @param string $id Unique batch identifier.
     * @param string $object Object type ('batch').
     * @param string $endpoint The endpoint shape for requests in the batch.
     * @param string $model Model identifier applied to the batch.
     * @param string $completionWindow The completion window (e.g. '24h').
     * @param BatchStatus $status Current status of the batch.
     * @param \DateTimeImmutable|null $createdAt Timestamp when the batch was created.
     * @param \DateTimeImmutable|null $finalizedAt Timestamp when the batch was finalized.
     * @param BatchRequestCounts|null $requestCounts Progress counts for total, completed, and failed requests.
     * @param BatchUsage|null $usage Token usage and billing details.
     * @param array<int, BatchResultItem>|null $results Inline results array (when completed).
     * @param array<string, mixed>|null $error Top-level error object if batch failed.
     * @param ResponseMetadata $metadata HTTP response metadata.
     */
    public function __construct(
        public string $id,
        public string $object,
        public string $endpoint,
        public string $model,
        public string $completionWindow,
        public BatchStatus $status,
        public ?\DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $finalizedAt,
        public ?BatchRequestCounts $requestCounts,
        public ?BatchUsage $usage,
        public ?array $results,
        public ?array $error,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Parses a timestamp or datetime string into DateTimeImmutable.
     *
     * @param mixed $value
     * @return \DateTimeImmutable|null
     */
    private static function parseDateTime(mixed $value): ?\DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || (is_string($value) && ctype_digit($value))) {
            return (new \DateTimeImmutable())->setTimestamp((int) $value);
        }

        if (is_string($value) && $value !== '') {
            try {
                return new \DateTimeImmutable($value);
            } catch (\Exception) {
                return null;
            }
        }

        return null;
    }

    /**
     * Instantiates BatchResponse from API response payload.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata $metadata
     * @return self
     */
    public static function fromArray(array $data, ResponseMetadata $metadata): self
    {
        $statusStr = $data['status'] ?? null;
        $status = is_string($statusStr) ? BatchStatus::fromString($statusStr) : BatchStatus::Unknown;

        $requestCounts = isset($data['request_counts']) && is_array($data['request_counts'])
            ? BatchRequestCounts::fromArray($data['request_counts'])
            : null;

        $usage = isset($data['usage']) && is_array($data['usage'])
            ? BatchUsage::fromArray($data['usage'])
            : null;

        $results = null;
        if (isset($data['results']) && is_array($data['results'])) {
            $results = [];
            foreach ($data['results'] as $item) {
                if (is_array($item)) {
                    $results[] = BatchResultItem::fromArray($item);
                }
            }
        }

        $error = isset($data['error']) && is_array($data['error']) ? $data['error'] : null;

        $id = $data['id'] ?? null;
        $object = $data['object'] ?? null;
        $endpoint = $data['endpoint'] ?? null;
        $model = $data['model'] ?? null;
        $completionWindow = $data['completion_window'] ?? null;

        return new self(
            id: is_string($id) ? $id : '',
            object: is_string($object) ? $object : 'batch',
            endpoint: is_string($endpoint) ? $endpoint : '',
            model: is_string($model) ? $model : '',
            completionWindow: is_string($completionWindow) ? $completionWindow : '24h',
            status: $status,
            createdAt: self::parseDateTime($data['created_at'] ?? null),
            finalizedAt: self::parseDateTime($data['finalized_at'] ?? null),
            requestCounts: $requestCounts,
            usage: $usage,
            results: $results,
            error: $error,
            metadata: $metadata,
        );
    }
}
