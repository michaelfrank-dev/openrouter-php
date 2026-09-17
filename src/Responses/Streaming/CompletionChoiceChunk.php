<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Streaming;

use MichaelFrank\OpenRouter\Enums\FinishReason;
use MichaelFrank\OpenRouter\Responses\ChoiceError;

/**
 * Class CompletionChoiceChunk
 *
 * Incremental choice delta returned by streaming completions.
 *
 * @package MichaelFrank\OpenRouter\Responses\Streaming
 */
final readonly class CompletionChoiceChunk
{
    /**
     * CompletionChoiceChunk constructor.
     *
     * @param int|null $index
     * @param ChatMessageChunk|null $delta
     * @param FinishReason|null $finishReason
     * @param string|null $nativeFinishReason
     * @param ChoiceError|null $error
     */
    public function __construct(
        public ?int $index,
        public ?ChatMessageChunk $delta,
        public ?FinishReason $finishReason,
        public ?string $nativeFinishReason,
        public ?ChoiceError $error,
    ) {
    }

    /**
     * Factory to build CompletionChoiceChunk from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $index = $data['index'] ?? null;
        $finish = $data['finish_reason'] ?? null;
        $native = $data['native_finish_reason'] ?? null;

        return new self(
            index: is_numeric($index) ? (int)$index : null,
            delta: isset($data['delta']) && is_array($data['delta'])
                ? ChatMessageChunk::fromArray($data['delta'])
                : null,
            finishReason: is_string($finish) ? FinishReason::fromString($finish) : null,
            nativeFinishReason: is_string($native) ? $native : null,
            error: isset($data['error']) && is_array($data['error']) ? ChoiceError::fromArray($data['error']) : null
        );
    }
}
