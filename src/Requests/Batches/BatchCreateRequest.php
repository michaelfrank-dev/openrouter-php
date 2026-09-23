<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Batches;

use MichaelFrank\OpenRouter\Exceptions\ValidationException;

/**
 * Class BatchCreateRequest
 *
 * Represents a batch submission request for the OpenRouter Batch API.
 *
 * @package MichaelFrank\OpenRouter\Requests\Batches
 * @see https://openrouter.ai/docs/guides/overview/batch-api#submit-a-batch
 */
final readonly class BatchCreateRequest implements \JsonSerializable
{
    /**
     * BatchCreateRequest constructor.
     *
     * @param string|BatchEndpoint $endpoint The API shape used by every request in the batch.
     * @param string $model The OpenRouter model slug applied to every request in the batch.
     * @param array<int, BatchItem> $requests Non-empty array of BatchItem requests.
     * @param BatchProviderRouting|null $provider Optional provider routing constraint (pinned provider).
     * @param string $completionWindow The batch completion window (defaults to '24h').
     * @throws ValidationException
     */
    public function __construct(
        public string|BatchEndpoint $endpoint,
        public string $model,
        public array $requests,
        public ?BatchProviderRouting $provider = null,
        public string $completionWindow = '24h',
    ) {
        $endpointStr = $this->endpoint instanceof BatchEndpoint ? $this->endpoint->value : $this->endpoint;
        if (trim($endpointStr) === '') {
            throw new ValidationException('Batch endpoint cannot be empty.');
        }

        if (trim($this->model) === '') {
            throw new ValidationException('Batch model cannot be empty.');
        }

        if (empty($this->requests)) {
            throw new ValidationException('Batch requests array cannot be empty.');
        }

        foreach ($this->requests as $index => $request) {
            if (!$request instanceof BatchItem) {
                throw new ValidationException(
                    "Item at index {$index} must be an instance of " . BatchItem::class
                );
            }
        }
    }

    /**
     * Serializes request payload ensuring stream-parsing key ordering:
     * endpoint, model, completion_window, (provider), and requests last.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $endpointStr = $this->endpoint instanceof BatchEndpoint ? $this->endpoint->value : $this->endpoint;

        $data = [
            'endpoint' => $endpointStr,
            'model' => $this->model,
            'completion_window' => $this->completionWindow,
        ];

        if ($this->provider !== null) {
            $data['provider'] = $this->provider->toArray();
        }

        $data['requests'] = array_map(
            static fn (BatchItem $item): array => $item->toArray(),
            $this->requests
        );

        return $data;
    }

    /**
     * Serializes to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
