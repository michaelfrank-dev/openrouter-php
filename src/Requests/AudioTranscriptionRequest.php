<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

use MichaelFrank\OpenRouter\Exceptions\ValidationException;

/**
 * Class AudioTranscriptionRequest
 *
 * Configures request payload structure for speech-to-text transcriptions.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final readonly class AudioTranscriptionRequest implements \JsonSerializable
{
    /**
     * AudioTranscriptionRequest constructor.
     *
     * @param string $model
     * @param string $audioData base64 encoded audio payload.
     * @param string $format m4a, mp3, webm, wav, ogg, etc.
     * @param string|null $language
     * @param float|null $temperature
     * @param ProviderPreferences|null $provider
     */
    public function __construct(
        public string $model,
        public string $audioData,
        public string $format,
        public ?string $language = null,
        public ?float $temperature = null,
        public ?ProviderPreferences $provider = null,
    ) {
    }

    /**
     * Factory to build a request directly from a file path.
     *
     * @param string $path
     * @param string $model
     * @param string|null $language
     * @return self
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function fromFile(string $path, string $model, ?string $language = null): self
    {
        if (!is_file($path)) {
            throw new ValidationException("Audio file not found: " . $path);
        }

        $bytes = file_get_contents($path);
        if ($bytes === false) {
            throw new ValidationException("Failed to read audio file: " . $path);
        }

        $base64 = base64_encode($bytes);
        $format = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return new self(
            model: $model,
            audioData: $base64,
            format: $format,
            language: $language
        );
    }

    /**
     * Converts to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'model' => $this->model,
            'input_audio' => [
                'data' => $this->audioData,
                'format' => $this->format,
            ],
        ];

        if ($this->language !== null) {
            $data['language'] = $this->language;
        }

        if ($this->temperature !== null) {
            $data['temperature'] = $this->temperature;
        }

        if ($this->provider !== null) {
            $data['provider'] = $this->provider->toArray();
        }

        return $data;
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
