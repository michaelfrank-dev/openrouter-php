<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ResponseFormat;

/**
 * Class JsonObjectResponseFormat
 *
 * Configures the model to output a valid JSON object.
 *
 * @package MichaelFrank\OpenRouter\Requests\ResponseFormat
 */
final readonly class JsonObjectResponseFormat implements ResponseFormat
{
    /**
     * Converts to payload array.
     *
     * @return array{type: string}
     */
    public function toArray(): array
    {
        return [
            'type' => 'json_object',
        ];
    }

    /**
     * JSON serialization format.
     *
     * @return array{type: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
