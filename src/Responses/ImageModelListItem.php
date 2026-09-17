<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class ImageModelListItem
 *
 * A single image model in the discovery listing.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ImageModelListItem
{
    /**
     * ImageModelListItem constructor.
     *
     * @param string $id Model slug (e.g., 'bytedance-seed/seedream-4.5')
     * @param string $name Display name
     * @param string $description Description of the model
     * @param int $created Unix timestamp of when the model was created
     * @param ImageModelArchitecture $architecture Modalities supported by the model
     * @param array<string, CapabilityDescriptor> $supportedParameters Supported capabilities
     * @param bool $supportsStreaming Whether SSE streaming is supported
     * @param string $endpoints Relative URL to the full per-endpoint records
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public int $created,
        public ImageModelArchitecture $architecture,
        public array $supportedParameters,
        public bool $supportsStreaming,
        public string $endpoints,
    ) {
    }

    /**
     * Factory to build ImageModelListItem from a payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id = isset($data['id']) && is_string($data['id']) ? $data['id'] : '';
        $name = isset($data['name']) && is_string($data['name']) ? $data['name'] : '';
        $description = isset($data['description']) && is_string($data['description']) ? $data['description'] : '';
        $created = isset($data['created']) && is_numeric($data['created']) ? (int)$data['created'] : 0;

        $architecture = isset($data['architecture']) && is_array($data['architecture'])
            ? ImageModelArchitecture::fromArray($data['architecture'])
            : new ImageModelArchitecture([], []);

        $params = [];
        if (isset($data['supported_parameters']) && is_array($data['supported_parameters'])) {
            foreach ($data['supported_parameters'] as $key => $paramData) {
                if (is_array($paramData)) {
                    $params[(string)$key] = CapabilityDescriptor::fromArray($paramData);
                }
            }
        }

        $supportsStreaming = (bool)($data['supports_streaming'] ?? false);
        $endpoints = isset($data['endpoints']) && is_string($data['endpoints']) ? $data['endpoints'] : '';

        return new self(
            id: $id,
            name: $name,
            description: $description,
            created: $created,
            architecture: $architecture,
            supportedParameters: $params,
            supportsStreaming: $supportsStreaming,
            endpoints: $endpoints
        );
    }
}
