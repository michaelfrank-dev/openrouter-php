<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class ChatCompletionResponse
 *
 * Represents the complete chat completion response data returned by the API.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ChatCompletionResponse
{
    /**
     * ChatCompletionResponse constructor.
     *
     * @param string $id
     * @param string $object
     * @param int $created
     * @param string $model
     * @param array<CompletionChoice> $choices
     * @param Usage|null $usage
     * @param string|null $systemFingerprint
     * @param ResponseMetadata $metadata
     */
    public function __construct(
        public string $id,
        public string $object,
        public int $created,
        public string $model,
        public array $choices,
        public ?Usage $usage,
        public ?string $systemFingerprint,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build ChatCompletionResponse from payload array and metadata.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata|null $metadata
     * @return self
     */
    public static function fromArray(array $data, ?ResponseMetadata $metadata = null): self
    {
        $choices = [];
        if (isset($data['choices']) && is_array($data['choices'])) {
            foreach ($data['choices'] as $choice) {
                if (is_array($choice)) {
                    $choices[] = CompletionChoice::fromArray($choice);
                }
            }
        }

        $id = $data['id'] ?? null;
        $object = $data['object'] ?? null;
        $created = $data['created'] ?? null;
        $model = $data['model'] ?? null;
        $systemFingerprint = $data['system_fingerprint'] ?? null;

        return new self(
            id: is_string($id) ? $id : '',
            object: is_string($object) ? $object : '',
            created: is_numeric($created) ? (int)$created : 0,
            model: is_string($model) ? $model : '',
            choices: $choices,
            usage: isset($data['usage']) && is_array($data['usage']) ? Usage::fromArray($data['usage']) : null,
            systemFingerprint: is_string($systemFingerprint) ? $systemFingerprint : null,
            metadata: $metadata ?? new ResponseMetadata()
        );
    }

    /**
     * Helper to retrieve the first choice generated, if any.
     *
     * @return CompletionChoice|null
     */
    public function getFirstChoice(): ?CompletionChoice
    {
        return $this->choices[0] ?? null;
    }
}
