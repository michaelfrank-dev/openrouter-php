<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class ModelInfo
 *
 * Detailed capabilities and metadata definitions for a single model.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ModelInfo
{
    /**
     * ModelInfo constructor.
     *
     * @param string $id
     * @param string $name
     * @param string|null $description
     * @param ModelPricing|null $pricing
     * @param int|null $contextLength
     * @param string|null $architecture
     * @param string|null $perRequestLimits
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public ?ModelPricing $pricing,
        public ?int $contextLength,
        public ?string $architecture,
        public ?string $perRequestLimits,
    ) {
    }

    /**
     * Factory to build ModelInfo from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;
        $name = $data['name'] ?? null;
        $desc = $data['description'] ?? null;
        $context = $data['context_length'] ?? null;

        $arch = null;
        if (isset($data['architecture'])) {
            $archData = $data['architecture'];
            if (is_array($archData)) {
                $arch = json_encode($archData);
            } elseif (is_scalar($archData)) {
                $arch = (string)$archData;
            }
            if ($arch === false) {
                $arch = null;
            }
        }

        $limits = null;
        if (isset($data['per_request_limits'])) {
            $limitsData = $data['per_request_limits'];
            if (is_array($limitsData)) {
                $limits = json_encode($limitsData);
            } elseif (is_scalar($limitsData)) {
                $limits = (string)$limitsData;
            }
            if ($limits === false) {
                $limits = null;
            }
        }

        return new self(
            id: is_string($id) ? $id : '',
            name: is_string($name) ? $name : '',
            description: is_string($desc) ? $desc : null,
            pricing: isset($data['pricing']) && is_array($data['pricing'])
                ? ModelPricing::fromArray($data['pricing'])
                : null,
            contextLength: is_numeric($context) ? (int)$context : null,
            architecture: $arch,
            perRequestLimits: $limits
        );
    }
}
