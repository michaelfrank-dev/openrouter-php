<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Tools;

/**
 * Interface ServerTool
 *
 * Interface for server-side tools supported by OpenRouter (e.g. web search, fetch).
 *
 * @package MichaelFrank\OpenRouter\Requests\Tools
 */
interface ServerTool extends \JsonSerializable
{
    /**
     * Gets the unique tool identifier type (e.g. 'openrouter:web_search').
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Converts tool parameters to a serializable payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
