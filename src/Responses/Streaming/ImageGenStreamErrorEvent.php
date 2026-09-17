<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Streaming;

/**
 * Class ImageGenStreamErrorEvent
 *
 * Emitted when streaming generation fails after the SSE response starts.
 *
 * @package MichaelFrank\OpenRouter\Responses\Streaming
 */
final readonly class ImageGenStreamErrorEvent implements ImageStreamEvent
{
    /**
     * ImageGenStreamErrorEvent constructor.
     *
     * @param array{message: string, code?: string|null, param?: string|null, type?: string|null} $error Error details
     * @param string $type The event type
     */
    public function __construct(
        public array $error,
        public string $type = 'error',
    ) {
    }

    /**
     * Gets the event type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Factory to build the event from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $errorData = $data['error'] ?? [];
        $type = $data['type'] ?? 'error';

        $errorArray = [
            'message' => 'Unknown stream error',
        ];

        if (is_array($errorData)) {
            $errorArray['message'] = isset($errorData['message']) && is_string($errorData['message'])
                ? $errorData['message']
                : 'Unknown stream error';

            if (isset($errorData['code'])) {
                $errorArray['code'] = is_string($errorData['code']) || is_numeric($errorData['code'])
                    ? (string)$errorData['code']
                    : null;
            }
            if (isset($errorData['param'])) {
                $errorArray['param'] = is_string($errorData['param'])
                    ? $errorData['param']
                    : null;
            }
            if (isset($errorData['type'])) {
                $errorArray['type'] = is_string($errorData['type'])
                    ? $errorData['type']
                    : null;
            }
        } elseif (is_string($errorData)) {
            $errorArray['message'] = $errorData;
        }

        return new self(
            error: $errorArray,
            type: is_string($type) ? $type : 'error'
        );
    }
}
