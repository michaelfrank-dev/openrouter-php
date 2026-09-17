<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\TokenDetails;

/**
 * Class CompletionTokensDetails
 *
 * Detailed breakdown of generated output token categories.
 *
 * @package MichaelFrank\OpenRouter\Responses\TokenDetails
 */
final readonly class CompletionTokensDetails
{
    /**
     * CompletionTokensDetails constructor.
     *
     * @param int|null $reasoningTokens
     * @param int|null $audioTokens
     * @param int|null $imageTokens
     */
    public function __construct(
        public ?int $reasoningTokens,
        public ?int $audioTokens,
        public ?int $imageTokens,
    ) {
    }

    /**
     * Factory to build CompletionTokensDetails from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $reasoning = $data['reasoning_tokens'] ?? null;
        $audio = $data['audio_tokens'] ?? null;
        $image = $data['image_tokens'] ?? null;

        return new self(
            reasoningTokens: is_numeric($reasoning) ? (int)$reasoning : null,
            audioTokens: is_numeric($audio) ? (int)$audio : null,
            imageTokens: is_numeric($image) ? (int)$image : null
        );
    }
}
