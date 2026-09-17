<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Enums\MessageRole;

/**
 * Class ChatMessage
 *
 * Represents a parsed response message from chat completions.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ChatMessage
{
    /**
     * ChatMessage constructor.
     *
     * @param MessageRole $role
     * @param string|null $content
     * @param string|null $reasoning
     * @param array<ToolCall> $toolCalls
     * @param string|null $name
     * @param string|null $toolCallId
     */
    public function __construct(
        public MessageRole $role,
        public ?string $content,
        public ?string $reasoning,
        public array $toolCalls,
        public ?string $name,
        public ?string $toolCallId,
    ) {
    }

    /**
     * Factory to build ChatMessage from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $toolCalls = [];
        if (isset($data['tool_calls']) && is_array($data['tool_calls'])) {
            foreach ($data['tool_calls'] as $tc) {
                if (is_array($tc)) {
                    $toolCalls[] = ToolCall::fromArray($tc);
                }
            }
        }

        $role = $data['role'] ?? null;
        $content = $data['content'] ?? null;
        $reasoning = $data['reasoning'] ?? null;
        $name = $data['name'] ?? null;
        $toolCallId = $data['tool_call_id'] ?? null;

        return new self(
            role: MessageRole::fromString(is_string($role) ? $role : 'assistant'),
            content: is_string($content) ? $content : null,
            reasoning: is_string($reasoning) ? $reasoning : null,
            toolCalls: $toolCalls,
            name: is_string($name) ? $name : null,
            toolCallId: is_string($toolCallId) ? $toolCallId : null
        );
    }

    /**
     * Checks if this message has active tool call triggers.
     *
     * @return bool
     */
    public function hasToolCalls(): bool
    {
        return $this->toolCalls !== [];
    }
}
