<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Streaming;

use MichaelFrank\OpenRouter\Enums\MessageRole;
use MichaelFrank\OpenRouter\Responses\ToolCall;

/**
 * Class ChatMessageChunk
 *
 * Incremental delta content segment from a streaming completions choice.
 *
 * @package MichaelFrank\OpenRouter\Responses\Streaming
 */
final readonly class ChatMessageChunk
{
    /**
     * ChatMessageChunk constructor.
     *
     * @param MessageRole|null $role
     * @param string|null $content
     * @param string|null $reasoning
     * @param array<ToolCall> $toolCalls
     */
    public function __construct(
        public ?MessageRole $role,
        public ?string $content,
        public ?string $reasoning,
        public array $toolCalls,
    ) {
    }

    /**
     * Factory to build ChatMessageChunk from payload array.
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

        return new self(
            role: is_string($role) ? MessageRole::fromString($role) : null,
            content: is_string($content) ? $content : null,
            reasoning: is_string($reasoning) ? $reasoning : null,
            toolCalls: $toolCalls
        );
    }

    /**
     * Checks if this chunk holds no meaningful delta updates.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->role === null
            && ($this->content === null || $this->content === '')
            && ($this->reasoning === null || $this->reasoning === '')
            && $this->toolCalls === [];
    }
}
