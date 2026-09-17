<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class AudioTranscriptionUsage
 *
 * Consumption details for audio transcribing request.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class AudioTranscriptionUsage
{
    /**
     * AudioTranscriptionUsage constructor.
     *
     * @param float|null $cost
     * @param int|null $inputTokens
     * @param int|null $outputTokens
     * @param float|null $seconds
     * @param int|null $totalTokens
     */
    public function __construct(
        public ?float $cost,
        public ?int $inputTokens,
        public ?int $outputTokens,
        public ?float $seconds,
        public ?int $totalTokens,
    ) {
    }

    /**
     * Factory to build AudioTranscriptionUsage from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $cost = $data['cost'] ?? null;
        $input = $data['input_tokens'] ?? null;
        $output = $data['output_tokens'] ?? null;
        $seconds = $data['seconds'] ?? null;
        $total = $data['total_tokens'] ?? null;

        return new self(
            cost: is_numeric($cost) ? (float)$cost : null,
            inputTokens: is_numeric($input) ? (int)$input : null,
            outputTokens: is_numeric($output) ? (int)$output : null,
            seconds: is_numeric($seconds) ? (float)$seconds : null,
            totalTokens: is_numeric($total) ? (int)$total : null
        );
    }
}
