<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;

/**
 * Class AudioSpeechResponse
 *
 * Holds lazy audio synthesis binary stream body and response metadata.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class AudioSpeechResponse
{
    /**
     * AudioSpeechResponse constructor.
     *
     * @param \Psr\Http\Message\StreamInterface $body
     * @param ResponseMetadata $metadata
     */
    public function __construct(
        public \Psr\Http\Message\StreamInterface $body,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build AudioSpeechResponse. Throws LogicException since audio is binary.
     *
     * @param array<string, mixed> $data
     * @param ResponseMetadata|null $metadata
     * @return self
     * @throws \LogicException
     */
    public static function fromArray(array $data, ?ResponseMetadata $metadata = null): self
    {
        throw new \LogicException('AudioSpeechResponse must be constructed with a StreamInterface directly.');
    }

    /**
     * Lazily reads the remainder of the audio stream to a string.
     *
     * @return string
     */
    public function getContents(): string
    {
        if ($this->body->isSeekable()) {
            $this->body->rewind();
        }
        return $this->body->getContents();
    }

    /**
     * Saves the audio stream content directly to a file, creating target folders if needed.
     *
     * @param string $path
     * @return void
     * @throws \RuntimeException
     */
    public function saveToFile(string $path): void
    {
        if ($path === '') {
            throw new ValidationException("Failed to save audio file to empty path.");
        }

        $dir = dirname($path);
        if ($dir !== '.' && !is_dir($dir)) {
            if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new ValidationException("Could not create directory: " . $dir);
            }
        }

        if (@file_put_contents($path, $this->getContents()) === false) {
            throw new ValidationException("Failed to save audio file to " . $path);
        }
    }
}
