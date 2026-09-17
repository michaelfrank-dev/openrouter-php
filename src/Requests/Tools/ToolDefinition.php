<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Tools;

/**
 * Class ToolDefinition
 *
 * Configures functional/traditional client-side tools.
 *
 * @package MichaelFrank\OpenRouter\Requests\Tools
 */
final readonly class ToolDefinition implements \JsonSerializable
{
    /**
     * ToolDefinition constructor.
     *
     * @param string $name
     * @param string|null $description
     * @param array<string, mixed>|null $parameters
     * @param bool $strict
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?array $parameters = null,
        public bool $strict = false,
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array{type: string, function: array<string, mixed>}
     */
    public function toArray(): array
    {
        $func = [
            'name' => $this->name,
        ];

        if ($this->description !== null) {
            $func['description'] = $this->description;
        }

        if ($this->parameters !== null) {
            $func['parameters'] = $this->parameters;
        }

        if ($this->strict) {
            $func['strict'] = true;
        }

        return [
            'type' => 'function',
            'function' => $func,
        ];
    }

    /**
     * JSON serialization format.
     *
     * @return array{type: string, function: array<string, mixed>}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
