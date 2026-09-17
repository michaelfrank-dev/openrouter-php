<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

use MichaelFrank\OpenRouter\Enums\ImageBackground;
use MichaelFrank\OpenRouter\Enums\ImageOutputFormat;
use MichaelFrank\OpenRouter\Enums\ImageQuality;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use MichaelFrank\OpenRouter\Requests\Messages\ContentPart\ImageContentPart;

/**
 * Class ImageGenerationRequest
 *
 * Configures the parameters for generating an image from a text prompt.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final class ImageGenerationRequest implements \JsonSerializable
{
    /**
     * ImageGenerationRequest constructor.
     *
     * @param string $model Image generation model
     * @param string $prompt Text description of desired image
     * @param string|null $aspectRatio Normalized aspect ratio (e.g. "1:1", "16:9", "auto")
     * @param ImageBackground|null $background Background treatment
     * @param array<ImageContentPart>|null $inputReferences Guidance images
     * @param int|null $n Number of images to generate (1-10)
     * @param int|null $outputCompression Compression level (0-100) for webp/jpeg
     * @param ImageOutputFormat|null $outputFormat Encoding of returned image bytes
     * @param ImageGenerationProviderPreferences|null $provider Provider preferences
     * @param ImageQuality|null $quality Rendering quality
     * @param ImageResolution|null $resolution Normalized resolution tier
     * @param int|null $seed Deterministic generation seed
     * @param string|null $size Convenience shorthand for output dimensions (e.g. "2K", "2048x2048")
     * @param bool|null $stream Whether to stream partial images
     * @throws ValidationException
     */
    public function __construct(
        public readonly string $model,
        public readonly string $prompt,
        public readonly ?string $aspectRatio = null,
        public readonly ?ImageBackground $background = null,
        public readonly ?array $inputReferences = null,
        public readonly ?int $n = null,
        public readonly ?int $outputCompression = null,
        public readonly ?ImageOutputFormat $outputFormat = null,
        public readonly ?ImageGenerationProviderPreferences $provider = null,
        public readonly ?ImageQuality $quality = null,
        public readonly ?ImageResolution $resolution = null,
        public readonly ?int $seed = null,
        public readonly ?string $size = null,
        public readonly ?bool $stream = null,
    ) {
        if (trim($model) === '') {
            throw new ValidationException('Model identifier cannot be empty.');
        }

        if (trim($prompt) === '') {
            throw new ValidationException('Prompt cannot be empty.');
        }

        if ($n !== null && ($n < 1 || $n > 10)) {
            throw new ValidationException('Parameter "n" must be between 1 and 10.');
        }

        if ($outputCompression !== null && ($outputCompression < 0 || $outputCompression > 100)) {
            throw new ValidationException('Parameter "outputCompression" must be between 0 and 100.');
        }
    }

    /**
     * Converts request to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'model' => $this->model,
            'prompt' => $this->prompt,
        ];

        if ($this->aspectRatio !== null) {
            $data['aspect_ratio'] = $this->aspectRatio;
        }

        if ($this->background !== null) {
            $data['background'] = $this->background->value;
        }

        if ($this->inputReferences !== null && $this->inputReferences !== []) {
            $data['input_references'] = array_map(
                static fn(ImageContentPart $ref): array => $ref->toArray(),
                $this->inputReferences
            );
        }

        if ($this->n !== null) {
            $data['n'] = $this->n;
        }

        if ($this->outputCompression !== null) {
            $data['output_compression'] = $this->outputCompression;
        }

        if ($this->outputFormat !== null) {
            $data['output_format'] = $this->outputFormat->value;
        }

        if ($this->provider !== null) {
            $data['provider'] = $this->provider->toArray();
        }

        if ($this->quality !== null) {
            $data['quality'] = $this->quality->value;
        }

        if ($this->resolution !== null) {
            $data['resolution'] = $this->resolution->value;
        }

        if ($this->seed !== null) {
            $data['seed'] = $this->seed;
        }

        if ($this->size !== null) {
            $data['size'] = $this->size;
        }

        if ($this->stream !== null) {
            $data['stream'] = $this->stream;
        }

        return $data;
    }

    /**
     * JSON serialization structure.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Returns a new instance with the updated model.
     *
     * @param string $model
     * @return self
     */
    public function withModel(string $model): self
    {
        return new self(
            model: $model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated prompt.
     *
     * @param string $prompt
     * @return self
     */
    public function withPrompt(string $prompt): self
    {
        return new self(
            model: $this->model,
            prompt: $prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated aspect ratio.
     *
     * @param string|null $aspectRatio
     * @return self
     */
    public function withAspectRatio(?string $aspectRatio): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated background option.
     *
     * @param ImageBackground|null $background
     * @return self
     */
    public function withBackground(?ImageBackground $background): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated input references.
     *
     * @param array<ImageContentPart>|null $inputReferences
     * @return self
     */
    public function withInputReferences(?array $inputReferences): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated image count.
     *
     * @param int|null $n
     * @return self
     */
    public function withN(?int $n): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated compression value.
     *
     * @param int|null $outputCompression
     * @return self
     */
    public function withOutputCompression(?int $outputCompression): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated output format.
     *
     * @param ImageOutputFormat|null $outputFormat
     * @return self
     */
    public function withOutputFormat(?ImageOutputFormat $outputFormat): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated provider preferences.
     *
     * @param ImageGenerationProviderPreferences|null $provider
     * @return self
     */
    public function withProvider(?ImageGenerationProviderPreferences $provider): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated quality tier.
     *
     * @param ImageQuality|null $quality
     * @return self
     */
    public function withQuality(?ImageQuality $quality): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated resolution tier.
     *
     * @param ImageResolution|null $resolution
     * @return self
     */
    public function withResolution(?ImageResolution $resolution): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated seed.
     *
     * @param int|null $seed
     * @return self
     */
    public function withSeed(?int $seed): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $seed,
            size: $this->size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated size.
     *
     * @param string|null $size
     * @return self
     */
    public function withSize(?string $size): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $size,
            stream: $this->stream
        );
    }

    /**
     * Returns a new instance with the updated stream parameter.
     *
     * @param bool|null $stream
     * @return self
     */
    public function withStream(?bool $stream): self
    {
        return new self(
            model: $this->model,
            prompt: $this->prompt,
            aspectRatio: $this->aspectRatio,
            background: $this->background,
            inputReferences: $this->inputReferences,
            n: $this->n,
            outputCompression: $this->outputCompression,
            outputFormat: $this->outputFormat,
            provider: $this->provider,
            quality: $this->quality,
            resolution: $this->resolution,
            seed: $this->seed,
            size: $this->size,
            stream: $stream
        );
    }
}
