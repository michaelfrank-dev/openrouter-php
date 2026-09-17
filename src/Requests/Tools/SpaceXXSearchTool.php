<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Tools;

use MichaelFrank\OpenRouter\Requests\ProviderRouting\XSearchFilter;

/**
 * Class SpaceXXSearchTool
 *
 * Configures SpaceXAI's native x_search tool directly for completions and Responses API.
 *
 * @package MichaelFrank\OpenRouter\Requests\Tools
 */
final readonly class SpaceXXSearchTool implements ServerTool
{
    /**
     * SpaceXXSearchTool constructor.
     *
     * @param XSearchFilter|null $filters Optional search filters (handles, date range, understanding flags).
     */
    public function __construct(
        public ?XSearchFilter $filters = null,
    ) {
    }

    /**
     * Gets the unique tool identifier type.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'x_search';
    }

    /**
     * Converts to payload array.
     *
     * @return array{type: string, parameters?: array<string, mixed>}
     */
    public function toArray(): array
    {
        $result = [
            'type' => $this->getType(),
        ];

        if ($this->filters !== null) {
            $filterData = $this->filters->toArray();
            if ($filterData !== []) {
                $result['parameters'] = $filterData;
            }
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
