<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class ChoiceError
 *
 * Represents an error details array inside a completion choice from the model.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ChoiceError
{
    /**
     * ChoiceError constructor.
     *
     * @param int|null $code
     * @param string $message
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public ?int $code,
        public string $message,
        public ?array $metadata,
    ) {
    }

    /**
     * Factory to build ChoiceError from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $code = $data['code'] ?? null;
        $message = $data['message'] ?? null;

        return new self(
            code: is_numeric($code) ? (int)$code : null,
            message: is_string($message) ? $message : '',
            metadata: isset($data['metadata']) && is_array($data['metadata']) ? $data['metadata'] : null
        );
    }
}
