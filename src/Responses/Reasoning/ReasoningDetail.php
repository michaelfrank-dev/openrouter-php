<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Reasoning;

use JsonSerializable;

/**
 * Class ReasoningDetail
 *
 * Represents a structured reasoning detail item (summary, encrypted, or text) from OpenRouter.
 *
 * @see https://openrouter.ai/docs/guides/reasoning-tokens#reasoning_details-array-structure
 * @package MichaelFrank\OpenRouter\Responses\Reasoning
 */
final readonly class ReasoningDetail implements JsonSerializable
{
    public const TYPE_SUMMARY = 'reasoning.summary';
    public const TYPE_ENCRYPTED = 'reasoning.encrypted';
    public const TYPE_TEXT = 'reasoning.text';

    /**
     * ReasoningDetail constructor.
     *
     * @param string $type The reasoning detail type (summary, encrypted, or text).
     * @param string|null $id Unique identifier for the reasoning detail.
     * @param string|null $format Provider format version identifier.
     * @param int|null $index Sequential index of the reasoning detail.
     * @param string|null $summary High-level summary of reasoning (for reasoning.summary).
     * @param string|null $data Encrypted reasoning data (for reasoning.encrypted).
     * @param string|null $text Raw text reasoning (for reasoning.text).
     * @param string|null $signature Cryptographic signature verification (for reasoning.text).
     */
    public function __construct(
        public string $type,
        public ?string $id = null,
        public ?string $format = null,
        public ?int $index = null,
        public ?string $summary = null,
        public ?string $data = null,
        public ?string $text = null,
        public ?string $signature = null,
    ) {
    }

    /**
     * Build ReasoningDetail from array payload.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $type = $data['type'] ?? self::TYPE_TEXT;
        $id = $data['id'] ?? null;
        $format = $data['format'] ?? null;
        $index = $data['index'] ?? null;
        $summary = $data['summary'] ?? null;
        $encData = $data['data'] ?? null;
        $text = $data['text'] ?? null;
        $signature = $data['signature'] ?? null;

        return new self(
            type: is_string($type) ? $type : self::TYPE_TEXT,
            id: is_string($id) ? $id : null,
            format: is_string($format) ? $format : null,
            index: is_numeric($index) ? (int)$index : null,
            summary: is_string($summary) ? $summary : null,
            data: is_string($encData) ? $encData : null,
            text: is_string($text) ? $text : null,
            signature: is_string($signature) ? $signature : null,
        );
    }

    /**
     * Converts to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'type' => $this->type,
        ];

        if ($this->id !== null) {
            $result['id'] = $this->id;
        }

        if ($this->format !== null) {
            $result['format'] = $this->format;
        }

        if ($this->index !== null) {
            $result['index'] = $this->index;
        }

        if ($this->summary !== null) {
            $result['summary'] = $this->summary;
        }

        if ($this->data !== null) {
            $result['data'] = $this->data;
        }

        if ($this->text !== null) {
            $result['text'] = $this->text;
        }

        if ($this->signature !== null) {
            $result['signature'] = $this->signature;
        }

        return $result;
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
