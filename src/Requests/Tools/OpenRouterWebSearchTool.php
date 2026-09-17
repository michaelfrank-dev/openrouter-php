<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Tools;

use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\SearchContextSize;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\UserLocation;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\WebSearchEngine;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\XSearchFilter;

/**
 * Class OpenRouterWebSearchTool
 *
 * Configures openrouter:web_search tool for completions.
 *
 * @package MichaelFrank\OpenRouter\Requests\Tools
 */
final readonly class OpenRouterWebSearchTool implements ServerTool
{
    /**
     * OpenRouterWebSearchTool constructor.
     *
     * @param WebSearchEngine|null $engine
     * @param int|null $maxResults
     * @param int|null $maxTotalResults
     * @param SearchContextSize|null $searchContextSize
     * @param int|null $maxCharacters
     * @param array<string>|null $allowedDomains
     * @param array<string>|null $excludedDomains
     * @param UserLocation|null $userLocation Approximate user location to geographically bias search engine results. Note: only biases results and does not inject location context into the model's reasoning.
     * @param int|null $maxUses
     * @param XSearchFilter|bool|null $xSearch Enables X/Twitter search for SpaceXAI/Grok models. Pass true or empty XSearchFilter to enable with no filters, or an XSearchFilter with handles/dates to restrict posts.
     * @throws ValidationException
     */
    public function __construct(
        public ?WebSearchEngine $engine = null,
        public ?int $maxResults = null,
        public ?int $maxTotalResults = null,
        public ?SearchContextSize $searchContextSize = null,
        public ?int $maxCharacters = null,
        public ?array $allowedDomains = null,
        public ?array $excludedDomains = null,
        public ?UserLocation $userLocation = null,
        public ?int $maxUses = null,
        public XSearchFilter|bool|null $xSearch = null,
    ) {
        if (
            $this->engine !== null && in_array(strtolower($this->engine->value), [
            WebSearchEngine::FIRECRAWL,
            WebSearchEngine::PARALLEL,
            WebSearchEngine::PERPLEXITY,
            ], true)
        ) {
            if ($this->allowedDomains !== null && $this->excludedDomains !== null) {
                throw new ValidationException(sprintf(
                    'allowedDomains and excludedDomains are mutually exclusive for engine "%s".',
                    $this->engine->value
                ));
            }
        }
    }

    /**
     * Gets the unique tool identifier type.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'openrouter:web_search';
    }

    /**
     * Converts to payload array.
     *
     * @return array{type: string, parameters?: array<string, mixed>}
     */
    public function toArray(): array
    {
        $params = [];

        if ($this->engine !== null) {
            $params['engine'] = $this->engine->value;
        }
        if ($this->maxResults !== null) {
            $params['max_results'] = $this->maxResults;
        }
        if ($this->maxTotalResults !== null) {
            $params['max_total_results'] = $this->maxTotalResults;
        }
        if ($this->searchContextSize !== null) {
            $params['search_context_size'] = $this->searchContextSize->value;
        }
        if ($this->maxCharacters !== null) {
            $params['max_characters'] = $this->maxCharacters;
        }
        if ($this->allowedDomains !== null) {
            $params['allowed_domains'] = $this->allowedDomains;
        }
        if ($this->excludedDomains !== null) {
            $params['excluded_domains'] = $this->excludedDomains;
        }
        if ($this->userLocation !== null) {
            $params['user_location'] = $this->userLocation->toArray();
        }
        if ($this->maxUses !== null) {
            $params['max_uses'] = $this->maxUses;
        }
        if ($this->xSearch !== null && $this->xSearch !== false) {
            if ($this->xSearch === true) {
                $params['x_search'] = new \stdClass();
            } else {
                $xSearchData = $this->xSearch->toArray();
                $params['x_search'] = $xSearchData === [] ? new \stdClass() : $xSearchData;
            }
        }

        $result = [
            'type' => $this->getType(),
        ];

        if ($params !== []) {
            $result['parameters'] = $params;
        }

        return $result;
    }

    /**
     * JSON serialization format.
     *
     * @return array{type: string, parameters?: array<string, mixed>}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
