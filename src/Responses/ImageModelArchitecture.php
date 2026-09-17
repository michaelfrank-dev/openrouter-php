<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class ImageModelArchitecture
 *
 * Holds input and output modalities for an image model.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ImageModelArchitecture
{
    /**
     * ImageModelArchitecture constructor.
     *
     * @param array<string> $inputModalities Supported input modalities
     * @param array<string> $outputModalities Supported output modalities
     */
    public function __construct(
        public array $inputModalities,
        public array $outputModalities,
    ) {
    }

    /**
     * Factory to build ImageModelArchitecture from a payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $inputs = isset($data['input_modalities']) && is_array($data['input_modalities'])
            ? array_map('strval', $data['input_modalities'])
            : [];
        $outputs = isset($data['output_modalities']) && is_array($data['output_modalities'])
            ? array_map('strval', $data['output_modalities'])
            : [];

        return new self(
            inputModalities: $inputs,
            outputModalities: $outputs
        );
    }
}
