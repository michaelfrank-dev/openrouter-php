<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

/**
 * Class AudioSpeechRequest
 *
 * Configures request payload structure for text-to-speech conversion.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final readonly class AudioSpeechRequest implements \JsonSerializable
{
    /**
     * AudioSpeechRequest constructor.
     *
     * @param string $model
     * @param string $input Text to synthesize.
     * @param string $voice Voice character to use.
     * @param string $responseFormat 'pcm', 'mp3', etc.
     * @param float|null $speed
     * @param ProviderPreferences|null $provider
     */
    public function __construct(
        public string $model,
        public string $input,
        public string $voice,
        public string $responseFormat = 'pcm',
        public ?float $speed = null,
        public ?ProviderPreferences $provider = null,
    ) {
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
            'input' => $this->input,
            'voice' => $this->voice,
            'response_format' => $this->responseFormat,
        ];

        if ($this->speed !== null) {
            $data['speed'] = $this->speed;
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
