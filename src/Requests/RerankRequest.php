<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

/**
 * Class RerankRequest
 *
 * Configures request payload structure for reranking.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final readonly class RerankRequest implements \JsonSerializable
{
    /**
     * RerankRequest constructor.
     *
     * @param string $model
     * @param string $query
     * @param array<string|array{text?: string, image?: string}> $documents
     * @param int|null $topN
     * @param ProviderPreferences|null $provider
     */
    public function __construct(
        public string $model,
        public string $query,
        public array $documents,
        public ?int $topN = null,
        public ?ProviderPreferences $provider = null,
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'model' => $this->model,
            'query' => $this->query,
            'documents' => $this->documents,
        ];

        if ($this->topN !== null) {
            $data['top_n'] = $this->topN;
        }

        if ($this->provider !== null) {
            $data['provider'] = $this->provider->toArray();
        }

        return $data;
    }

    /**
     * JSON serialization format.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
