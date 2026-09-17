<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ResponseFormat;

/**
 * Class JsonSchemaResponseFormat
 *
 * Configures the model to output structured data matching a specific JSON Schema.
 *
 * @package MichaelFrank\OpenRouter\Requests\ResponseFormat
 */
final readonly class JsonSchemaResponseFormat implements ResponseFormat
{
    /**
     * JsonSchemaResponseFormat constructor.
     *
     * @param string $name
     * @param array<string, mixed> $schema
     * @param bool $strict
     */
    public function __construct(
        public string $name,
        public array $schema,
        public bool $strict = true,
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array{type: string, json_schema: array{name: string, schema: array<string, mixed>, strict: bool}}
     */
    public function toArray(): array
    {
        return [
            'type' => 'json_schema',
            'json_schema' => [
                'name' => $this->name,
                'schema' => $this->schema,
                'strict' => $this->strict,
            ],
        ];
    }

    /**
     * JSON serialization format.
     *
     * @return array{type: string, json_schema: array{name: string, schema: array<string, mixed>, strict: bool}}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
