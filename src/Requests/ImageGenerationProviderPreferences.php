<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

use MichaelFrank\OpenRouter\Requests\ProviderRouting\ProviderSort;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\ProviderSortConfig;

/**
 * Class ImageGenerationProviderPreferences
 *
 * Configures routing preferences and provider-specific configurations for image generation.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final class ImageGenerationProviderPreferences implements \JsonSerializable
{
    /**
     * ImageGenerationProviderPreferences constructor.
     *
     * @param bool|null $allowFallbacks
     * @param array<string>|null $ignore
     * @param array<string>|null $only
     * @param array<string, array<string, mixed>>|null $options Provider specific options
     * @param array<string>|null $order
     * @param ProviderSort|ProviderSortConfig|null $sort
     */
    public function __construct(
        public readonly ?bool $allowFallbacks = null,
        public readonly ?array $ignore = null,
        public readonly ?array $only = null,
        public readonly ?array $options = null,
        public readonly ?array $order = null,
        public readonly null|ProviderSort|ProviderSortConfig $sort = null,
    ) {
    }

    /**
     * Converts properties to serializable array following endpoint schemas.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->allowFallbacks !== null) {
            $data['allow_fallbacks'] = $this->allowFallbacks;
        }

        if ($this->ignore !== null && $this->ignore !== []) {
            $data['ignore'] = $this->ignore;
        }

        if ($this->only !== null && $this->only !== []) {
            $data['only'] = $this->only;
        }

        if ($this->options !== null && $this->options !== []) {
            $data['options'] = $this->options;
        }

        if ($this->order !== null && $this->order !== []) {
            $data['order'] = $this->order;
        }

        if ($this->sort !== null) {
            if ($this->sort instanceof ProviderSort) {
                $data['sort'] = $this->sort->value;
            } else {
                $data['sort'] = $this->sort->jsonSerialize();
            }
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
     * Returns a new instance with the updated allow fallbacks preference.
     *
     * @param bool|null $allowFallbacks
     * @return self
     */
    public function withAllowFallbacks(?bool $allowFallbacks): self
    {
        return new self(
            allowFallbacks: $allowFallbacks,
            ignore: $this->ignore,
            only: $this->only,
            options: $this->options,
            order: $this->order,
            sort: $this->sort
        );
    }

    /**
     * Returns a new instance with the updated ignore list.
     *
     * @param array<string>|null $ignore
     * @return self
     */
    public function withIgnore(?array $ignore): self
    {
        return new self(
            allowFallbacks: $this->allowFallbacks,
            ignore: $ignore,
            only: $this->only,
            options: $this->options,
            order: $this->order,
            sort: $this->sort
        );
    }

    /**
     * Returns a new instance with the updated only list.
     *
     * @param array<string>|null $only
     * @return self
     */
    public function withOnly(?array $only): self
    {
        return new self(
            allowFallbacks: $this->allowFallbacks,
            ignore: $this->ignore,
            only: $only,
            options: $this->options,
            order: $this->order,
            sort: $this->sort
        );
    }

    /**
     * Returns a new instance with the updated provider-specific options.
     *
     * @param array<string, array<string, mixed>>|null $options
     * @return self
     */
    public function withOptions(?array $options): self
    {
        return new self(
            allowFallbacks: $this->allowFallbacks,
            ignore: $this->ignore,
            only: $this->only,
            options: $options,
            order: $this->order,
            sort: $this->sort
        );
    }

    /**
     * Returns a new instance with the updated order preference.
     *
     * @param array<string>|null $order
     * @return self
     */
    public function withOrder(?array $order): self
    {
        return new self(
            allowFallbacks: $this->allowFallbacks,
            ignore: $this->ignore,
            only: $this->only,
            options: $this->options,
            order: $order,
            sort: $this->sort
        );
    }

    /**
     * Returns a new instance with the updated sort preference.
     *
     * @param ProviderSort|ProviderSortConfig|null $sort
     * @return self
     */
    public function withSort(null|ProviderSort|ProviderSortConfig $sort): self
    {
        return new self(
            allowFallbacks: $this->allowFallbacks,
            ignore: $this->ignore,
            only: $this->only,
            options: $this->options,
            order: $this->order,
            sort: $sort
        );
    }
}
