<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

use MichaelFrank\OpenRouter\Requests\Messages\MessageInterface;

/**
 * Class CompletionRequest
 *
 * Represents a complete request structure for chat completions.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final readonly class CompletionRequest implements \JsonSerializable
{
    /**
     * CompletionRequest constructor.
     *
     * @param string|array<string> $model Single model name, or list of models for fallback routing.
     * @param array<MessageInterface> $messages
     * @param CompletionOptions $options
     */
    public function __construct(
        public string|array $model,
        public array $messages,
        public CompletionOptions $options = new CompletionOptions(),
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'model' => $this->model,
            'messages' => array_map(
                static fn(MessageInterface $m): array => $m->toArray(),
                $this->messages
            ),
        ];

        return array_merge($payload, $this->options->toArray());
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
