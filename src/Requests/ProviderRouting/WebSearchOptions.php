<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

/**
 * Class WebSearchOptions
 *
 * Configures native web search options for Chat Completion requests.
 *
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class WebSearchOptions implements \JsonSerializable
{
    /**
     * WebSearchOptions constructor.
     *
     * @param SearchContextSize|null $searchContextSize
     */
    public function __construct(
        public ?SearchContextSize $searchContextSize = null,
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->searchContextSize !== null) {
            $data['search_context_size'] = $this->searchContextSize->value;
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
