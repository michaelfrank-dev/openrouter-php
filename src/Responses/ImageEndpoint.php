<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class ImageEndpoint
 *
 * Represents an endpoint that serves a given image model.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ImageEndpoint
{
    /**
     * ImageEndpoint constructor.
     *
     * @param array<string> $allowedPassthroughParameters Provider-specific options accepted
     * @param array<ImagePricingEntry> $pricing Billable pricing lines for this endpoint
     * @param string $providerName Provider display name
     * @param string $providerSlug Provider slug
     * @param string|null $providerTag Provider tag for request-side selection
     * @param array<string, CapabilityDescriptor> $supportedParameters The parameters this endpoint accepts
     * @param bool $supportsStreaming Whether SSE streaming is supported
     */
    public function __construct(
        public array $allowedPassthroughParameters,
        public array $pricing,
        public string $providerName,
        public string $providerSlug,
        public ?string $providerTag,
        public array $supportedParameters,
        public bool $supportsStreaming,
    ) {
    }

    /**
     * Factory to build ImageEndpoint from a payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $passthroughs = isset($data['allowed_passthrough_parameters']) && is_array($data['allowed_passthrough_parameters'])
            ? array_map('strval', $data['allowed_passthrough_parameters'])
            : [];

        $pricing = [];
        if (isset($data['pricing']) && is_array($data['pricing'])) {
            foreach ($data['pricing'] as $pricingData) {
                if (is_array($pricingData)) {
                    $pricing[] = ImagePricingEntry::fromArray($pricingData);
                }
            }
        }

        $providerName = isset($data['provider_name']) && is_string($data['provider_name']) ? $data['provider_name'] : '';
        $providerSlug = isset($data['provider_slug']) && is_string($data['provider_slug']) ? $data['provider_slug'] : '';
        $providerTag = isset($data['provider_tag']) && is_string($data['provider_tag']) ? $data['provider_tag'] : null;

        $params = [];
        if (isset($data['supported_parameters']) && is_array($data['supported_parameters'])) {
            foreach ($data['supported_parameters'] as $key => $paramData) {
                if (is_array($paramData)) {
                    $params[(string)$key] = CapabilityDescriptor::fromArray($paramData);
                }
            }
        }

        $supportsStreaming = (bool)($data['supports_streaming'] ?? false);

        return new self(
            allowedPassthroughParameters: $passthroughs,
            pricing: $pricing,
            providerName: $providerName,
            providerSlug: $providerSlug,
            providerTag: $providerTag,
            supportedParameters: $params,
            supportsStreaming: $supportsStreaming
        );
    }
}
