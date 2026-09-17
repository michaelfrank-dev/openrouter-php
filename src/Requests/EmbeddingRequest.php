<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

use MichaelFrank\OpenRouter\Requests\Messages\ContentPart\ContentPart;

/**
 * Class EmbeddingRequest
 *
 * Configures request payload structure for embeddings.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final readonly class EmbeddingRequest implements \JsonSerializable
{
    /**
     * EmbeddingRequest constructor.
     *
     * @param string $model
     * @param string|array<mixed> $input Supports string, array of strings, array of ContentParts, or array of integer token IDs.
     * @param string|null $encodingFormat
     * @param int|null $dimensions
     * @param string|null $inputType
     * @param ProviderPreferences|null $provider
     * @param string|null $user
     */
    public function __construct(
        public string $model,
        public string|array $input,
        public ?string $encodingFormat = null,
        public ?int $dimensions = null,
        public ?string $inputType = null,
        public ?ProviderPreferences $provider = null,
        public ?string $user = null,
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $inputVal = $this->input;
        if (is_array($inputVal)) {
            $inputVal = array_map(
                static function (mixed $part): mixed {
                    if ($part instanceof ContentPart) {
                        return $part->toArray();
                    }
                    return $part;
                },
                $inputVal
            );
        }

        $data = [
            'model' => $this->model,
            'input' => $inputVal,
        ];

        if ($this->encodingFormat !== null) {
            $data['encoding_format'] = $this->encodingFormat;
        }
        if ($this->dimensions !== null) {
            $data['dimensions'] = $this->dimensions;
        }
        if ($this->inputType !== null) {
            $data['input_type'] = $this->inputType;
        }
        if ($this->provider !== null) {
            $data['provider'] = $this->provider->toArray();
        }
        if ($this->user !== null) {
            $data['user'] = $this->user;
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
