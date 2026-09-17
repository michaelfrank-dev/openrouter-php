<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class AudioTranscriptionResponse
 *
 * Holds generated transcription text, usage details, and query metadata.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class AudioTranscriptionResponse
{
    /**
     * AudioTranscriptionResponse constructor.
     *
     * @param string $text
     * @param AudioTranscriptionUsage|null $usage
     * @param ResponseMetadata $metadata
     */
    public function __construct(
        public string $text,
        public ?AudioTranscriptionUsage $usage,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build AudioTranscriptionResponse from payload array and metadata.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata|null $metadata
     * @return self
     */
    public static function fromArray(array $data, ?ResponseMetadata $metadata = null): self
    {
        $text = $data['text'] ?? null;

        return new self(
            text: is_string($text) ? $text : '',
            usage: isset($data['usage']) && is_array($data['usage'])
                ? AudioTranscriptionUsage::fromArray($data['usage'])
                : null,
            metadata: $metadata ?? new ResponseMetadata()
        );
    }
}
