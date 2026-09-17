<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class ModelPricing
 *
 * Cost definitions per category for a single model.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ModelPricing
{
    /**
     * ModelPricing constructor.
     *
     * @param string|null $prompt
     * @param string|null $completion
     * @param string|null $image
     * @param string|null $request
     */
    public function __construct(
        public ?string $prompt,
        public ?string $completion,
        public ?string $image,
        public ?string $request,
    ) {
    }

    /**
     * Factory to build ModelPricing from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $prompt = $data['prompt'] ?? null;
        $completion = $data['completion'] ?? null;
        $image = $data['image'] ?? null;
        $request = $data['request'] ?? null;

        return new self(
            prompt: is_string($prompt) ? $prompt : null,
            completion: is_string($completion) ? $completion : null,
            image: is_string($image) ? $image : null,
            request: is_string($request) ? $request : null
        );
    }
}
