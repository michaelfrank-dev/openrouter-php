<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Batches;

use MichaelFrank\OpenRouter\Exceptions\ValidationException;

/**
 * Class BatchItem
 *
 * Represents an individual request item within a batch submission.
 *
 * @package MichaelFrank\OpenRouter\Requests\Batches
 */
final readonly class BatchItem implements \JsonSerializable
{
    /**
     * BatchItem constructor.
     *
     * @param string $customId Unique identifier for this request within the batch.
     * @param array<string, mixed>|\JsonSerializable $body The request body matching the batch endpoint shape.
     * @throws ValidationException
     */
    public function __construct(
        public string $customId,
        public array|\JsonSerializable $body
    ) {
        if (trim($this->customId) === '') {
            throw new ValidationException('Batch item customId cannot be empty.');
        }
    }

    /**
     * Converts to an array representation.
     *
     * @return array{custom_id: string, body: mixed}
     */
    public function toArray(): array
    {
        $bodyData = $this->body instanceof \JsonSerializable
            ? $this->body->jsonSerialize()
            : $this->body;

        return [
            'custom_id' => $this->customId,
            'body' => $bodyData,
        ];
    }

    /**
     * Serializes to JSON.
     *
     * @return array{custom_id: string, body: mixed}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
