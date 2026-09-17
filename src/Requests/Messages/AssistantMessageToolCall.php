<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Messages;

/**
 * Class AssistantMessageToolCall
 *
 * Represents a tool call request returned by/sent as part of an assistant message history.
 *
 * @package MichaelFrank\OpenRouter\Requests\Messages
 */
final readonly class AssistantMessageToolCall implements \JsonSerializable
{
    /**
     * AssistantMessageToolCall constructor.
     *
     * @param string $id
     * @param string $type
     * @param array{name: string, arguments: string} $function
     */
    public function __construct(
        public string $id,
        public string $type,
        public array $function,
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array{id: string, type: string, function: array{name: string, arguments: string}}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'function' => $this->function,
        ];
    }

    /**
     * JSON serialization format.
     *
     * @return array{id: string, type: string, function: array{name: string, arguments: string}}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
