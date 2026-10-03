<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Messages;

use MichaelFrank\OpenRouter\Responses\Reasoning\ReasoningDetail;

/**
 * Class AssistantMessage
 *
 * Represents an assistant response message in conversation history.
 *
 * @package MichaelFrank\OpenRouter\Requests\Messages
 */
final readonly class AssistantMessage implements MessageInterface
{
    /**
     * AssistantMessage constructor.
     *
     * @param string|null $content
     * @param array<AssistantMessageToolCall>|null $toolCalls
     * @param string|null $name
     * @param string|null $reasoning Plaintext reasoning to preserve across multi-turn tool calling.
     * @param array<ReasoningDetail|array<string, mixed>>|null $reasoningDetails Structured reasoning blocks to preserve.
     */
    public function __construct(
        public ?string $content = null,
        public ?array $toolCalls = null,
        public ?string $name = null,
        public ?string $reasoning = null,
        public ?array $reasoningDetails = null,
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'role' => 'assistant',
            'content' => $this->content,
        ];

        if ($this->toolCalls !== null) {
            $data['tool_calls'] = array_map(
                static fn(AssistantMessageToolCall $tc): array => $tc->toArray(),
                $this->toolCalls
            );
        }

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->reasoning !== null) {
            $data['reasoning'] = $this->reasoning;
        }

        if ($this->reasoningDetails !== null && $this->reasoningDetails !== []) {
            $data['reasoning_details'] = array_map(
                static fn(ReasoningDetail|array $item): array => $item instanceof ReasoningDetail
                    ? $item->toArray()
                    : $item,
                $this->reasoningDetails
            );
        }

        return $data;
    }

    /**
     * JSON serialization format.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
