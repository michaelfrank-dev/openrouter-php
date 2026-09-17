<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\TokenDetails;

/**
 * Class PromptTokensDetails
 *
 * Detailed breakdown of token categories in the user prompt.
 *
 * @package MichaelFrank\OpenRouter\Responses\TokenDetails
 */
final readonly class PromptTokensDetails
{
    /**
     * PromptTokensDetails constructor.
     *
     * @param int|null $cachedTokens
     * @param int|null $cacheWriteTokens
     * @param int|null $audioTokens
     * @param int|null $videoTokens
     * @param int|null $fileTokens
     */
    public function __construct(
        public ?int $cachedTokens,
        public ?int $cacheWriteTokens,
        public ?int $audioTokens,
        public ?int $videoTokens,
        public ?int $fileTokens = null,
    ) {
    }

    /**
     * Factory to build PromptTokensDetails from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $cached = $data['cached_tokens'] ?? null;
        $write = $data['cache_write_tokens'] ?? null;
        $audio = $data['audio_tokens'] ?? null;
        $video = $data['video_tokens'] ?? null;
        $file = $data['file_tokens'] ?? null;

        return new self(
            cachedTokens: is_numeric($cached) ? (int)$cached : null,
            cacheWriteTokens: is_numeric($write) ? (int)$write : null,
            audioTokens: is_numeric($audio) ? (int)$audio : null,
            videoTokens: is_numeric($video) ? (int)$video : null,
            fileTokens: is_numeric($file) ? (int)$file : null
        );
    }
}
