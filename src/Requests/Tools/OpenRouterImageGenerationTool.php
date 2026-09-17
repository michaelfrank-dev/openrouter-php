<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Tools;

use MichaelFrank\OpenRouter\Enums\ImageBackground;
use MichaelFrank\OpenRouter\Enums\ImageOutputFormat;
use MichaelFrank\OpenRouter\Enums\ImageQuality;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;

/**
 * Class OpenRouterImageGenerationTool
 *
 * Configures the openrouter:image_generation server-side tool.
 *
 * @package MichaelFrank\OpenRouter\Requests\Tools
 */
final readonly class OpenRouterImageGenerationTool implements ServerTool
{
    /**
     * OpenRouterImageGenerationTool constructor.
     *
     * @param string|null $model Which image generation model to use.
     * @param ImageQuality|null $quality Image quality level.
     * @param string|null $aspectRatio Aspect ratio (e.g. "16:9", "1:1").
     * @param string|null $size Convenience size (e.g. "1024x1024").
     * @param ImageBackground|null $background Background style.
     * @param ImageOutputFormat|null $outputFormat Output format.
     * @param int|null $outputCompression Compression level (0-100) for lossy formats.
     * @param string|null $moderation Content moderation level (e.g. "auto", "low").
     * @throws ValidationException
     */
    public function __construct(
        public ?string $model = null,
        public ?ImageQuality $quality = null,
        public ?string $aspectRatio = null,
        public ?string $size = null,
        public ?ImageBackground $background = null,
        public ?ImageOutputFormat $outputFormat = null,
        public ?int $outputCompression = null,
        public ?string $moderation = null,
    ) {
        if ($this->outputCompression !== null && ($this->outputCompression < 0 || $this->outputCompression > 100)) {
            throw new ValidationException('Parameter "outputCompression" must be between 0 and 100.');
        }
    }

    /**
     * Gets the unique tool identifier type.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'openrouter:image_generation';
    }

    /**
     * Converts tool parameters to a serializable payload array.
     *
     * @return array{type: string, parameters?: array<string, mixed>}
     */
    public function toArray(): array
    {
        $params = [];

        if ($this->model !== null) {
            $params['model'] = $this->model;
        }
        if ($this->quality !== null) {
            $params['quality'] = $this->quality->value;
        }
        if ($this->aspectRatio !== null) {
            $params['aspect_ratio'] = $this->aspectRatio;
        }
        if ($this->size !== null) {
            $params['size'] = $this->size;
        }
        if ($this->background !== null) {
            $params['background'] = $this->background->value;
        }
        if ($this->outputFormat !== null) {
            $params['output_format'] = $this->outputFormat->value;
        }
        if ($this->outputCompression !== null) {
            $params['output_compression'] = $this->outputCompression;
        }
        if ($this->moderation !== null) {
            $params['moderation'] = $this->moderation;
        }

        $result = [
            'type' => $this->getType(),
        ];

        if ($params !== []) {
            $result['parameters'] = $params;
        }

        return $result;
    }

    /**
     * JSON serialization format.
     *
     * @return array{type: string, parameters?: array<string, mixed>}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
