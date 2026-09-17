<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Tools;

use MichaelFrank\OpenRouter\Requests\ProviderRouting\WebFetchEngine;

/**
 * Class OpenRouterWebFetchTool
 *
 * Configures openrouter:web_fetch tool for completions.
 *
 * @package MichaelFrank\OpenRouter\Requests\Tools
 */
final readonly class OpenRouterWebFetchTool implements ServerTool
{
    /**
     * OpenRouterWebFetchTool constructor.
     *
     * @param WebFetchEngine|null $engine
     * @param string|null $url
     * @param int|null $maxContentTokens
     * @param array<string>|null $allowedDomains
     * @param array<string>|null $blockedDomains
     * @param int|null $maxUses
     */
    public function __construct(
        public ?WebFetchEngine $engine = null,
        public ?string $url = null,
        public ?int $maxContentTokens = null,
        public ?array $allowedDomains = null,
        public ?array $blockedDomains = null,
        public ?int $maxUses = null,
    ) {
    }

    /**
     * Gets the unique tool identifier type.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'openrouter:web_fetch';
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
        if ($this->url !== null) {
            $params['url'] = $this->url;
        }
        if ($this->maxContentTokens !== null) {
            $params['max_content_tokens'] = $this->maxContentTokens;
        }
        if ($this->allowedDomains !== null) {
            $params['allowed_domains'] = $this->allowedDomains;
        }
        if ($this->blockedDomains !== null) {
            $params['blocked_domains'] = $this->blockedDomains;
        }
        if ($this->maxUses !== null) {
            $params['max_uses'] = $this->maxUses;
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
