<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Messages;

/**
 * Class ToolMessage
 *
 * Represents a message returned by a tool execution.
 *
 * @package MichaelFrank\OpenRouter\Requests\Messages
 */
final readonly class ToolMessage implements MessageInterface
{
    /**
     * ToolMessage constructor.
     *
     * @param string $content
     * @param string $toolCallId
     */
    public function __construct(
        public string $content,
        public string $toolCallId,
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array{role: string, content: string, tool_call_id: string}
     */
    public function toArray(): array
    {
        return [
            'role' => 'tool',
            'content' => $this->content,
            'tool_call_id' => $this->toolCallId,
        ];
    }

    /**
     * JSON serialization format.
     *
     * @return array{role: string, content: string, tool_call_id: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
