<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

use MichaelFrank\OpenRouter\Requests\ProviderRouting\PercentileCutoffs;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\ProviderSort;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\ProviderSortConfig;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\Quantization;

/**
 * Class ProviderPreferences
 *
 * Configures routing preferences to filter or prioritize particular model providers.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final class ProviderPreferences implements \JsonSerializable
{
    /**
     * ProviderPreferences constructor.
     *
     * @param array<string>|null $order
     * @param ProviderSort|ProviderSortConfig|null $sort
     * @param bool|null $allowFallbacks
     * @param bool|null $requireParameters
     * @param bool|null $enforceDistillableText
     * @param bool|null $zdr
     * @param string|null $dataCollection 'allow' or 'deny'
     * @param array<string>|null $only
     * @param array<string>|null $ignore
     * @param float|PercentileCutoffs|null $preferredMinThroughput
     * @param float|PercentileCutoffs|null $preferredMaxLatency
     * @param array{prompt?: string, completion?: string, image?: string, request?: string, audio?: string}|null $maxPrice
     * @param array<string>|null $requireFeature
     * @param array<Quantization>|null $quantizations
     */
    public function __construct(
        public readonly ?array $order = null,
        public readonly null|ProviderSort|ProviderSortConfig $sort = null,
        public readonly ?bool $allowFallbacks = null,
        public readonly ?bool $requireParameters = null,
        public readonly ?bool $enforceDistillableText = null,
        public readonly ?bool $zdr = null,
        public readonly ?string $dataCollection = 'allow',
        public readonly ?array $only = null,
        public readonly ?array $ignore = null,
        public readonly float|PercentileCutoffs|null $preferredMinThroughput = null,
        public readonly float|PercentileCutoffs|null $preferredMaxLatency = null,
        public readonly ?array $maxPrice = null,
        public readonly ?array $requireFeature = null,
        public readonly ?array $quantizations = null,
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

        if ($this->allowFallbacks !== null) {
            $data['allow_fallbacks'] = $this->allowFallbacks;
        }

        if ($this->requireParameters !== null) {
            $data['require_parameters'] = $this->requireParameters;
        }

        if ($this->enforceDistillableText !== null) {
            $data['enforce_distillable_text'] = $this->enforceDistillableText;
        }

        if ($this->zdr !== null) {
            $data['zdr'] = $this->zdr;
        }

        if ($this->dataCollection !== null) {
            $data['data_collection'] = $this->dataCollection;
        }

        if ($this->only !== null && $this->only !== []) {
            $data['only'] = $this->only;
        }

        if ($this->ignore !== null && $this->ignore !== []) {
            $data['ignore'] = $this->ignore;
        }

        if ($this->preferredMinThroughput !== null) {
            if ($this->preferredMinThroughput instanceof PercentileCutoffs) {
                $data['preferred_min_throughput'] = $this->preferredMinThroughput->jsonSerialize();
            } else {
                $data['preferred_min_throughput'] = $this->preferredMinThroughput;
            }
        }

        if ($this->preferredMaxLatency !== null) {
            if ($this->preferredMaxLatency instanceof PercentileCutoffs) {
                $data['preferred_max_latency'] = $this->preferredMaxLatency->jsonSerialize();
            } else {
                $data['preferred_max_latency'] = $this->preferredMaxLatency;
            }
        }

        if ($this->maxPrice !== null && $this->maxPrice !== []) {
            $data['max_price'] = $this->maxPrice;
        }

        if ($this->requireFeature !== null && $this->requireFeature !== []) {
            $data['require_feature'] = $this->requireFeature;
        }

        if ($this->quantizations !== null && $this->quantizations !== []) {
            $data['quantizations'] = array_map(
                static fn(Quantization $q): string => $q->value,
                $this->quantizations
            );
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
     * Returns a new instance with the updated order preference.
     *
     * @param array<string>|null $order
     * @return self
     */
    public function withOrder(?array $order): self
    {
        return new self(
            order: $order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
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
            order: $this->order,
            sort: $sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
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
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated require parameters preference.
     *
     * @param bool|null $requireParameters
     * @return self
     */
    public function withRequireParameters(?bool $requireParameters): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated enforce distillable text preference.
     *
     * @param bool|null $enforceDistillableText
     * @return self
     */
    public function withEnforceDistillableText(?bool $enforceDistillableText): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated zero data retention (zdr) preference.
     *
     * @param bool|null $zdr
     * @return self
     */
    public function withZdr(?bool $zdr): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated data collection preference.
     *
     * @param string|null $dataCollection
     * @return self
     */
    public function withDataCollection(?string $dataCollection): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated 'only' routing filter.
     *
     * @param array<string>|null $only
     * @return self
     */
    public function withOnly(?array $only): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated 'ignore' routing filter.
     *
     * @param array<string>|null $ignore
     * @return self
     */
    public function withIgnore(?array $ignore): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated minimum throughput filter.
     *
     * @param float|PercentileCutoffs|null $preferredMinThroughput
     * @return self
     */
    public function withPreferredMinThroughput(float|PercentileCutoffs|null $preferredMinThroughput): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated maximum latency filter.
     *
     * @param float|PercentileCutoffs|null $preferredMaxLatency
     * @return self
     */
    public function withPreferredMaxLatency(float|PercentileCutoffs|null $preferredMaxLatency): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated maximum price filter.
     *
     * @param array{prompt?: string, completion?: string, image?: string, request?: string, audio?: string}|null $maxPrice
     * @return self
     */
    public function withMaxPrice(?array $maxPrice): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated required features preference.
     *
     * @param array<string>|null $requireFeature
     * @return self
     */
    public function withRequireFeature(?array $requireFeature): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $requireFeature,
            quantizations: $this->quantizations,
        );
    }

    /**
     * Returns a new instance with the updated quantizations preferences.
     *
     * @param array<Quantization>|null $quantizations
     * @return self
     */
    public function withQuantizations(?array $quantizations): self
    {
        return new self(
            order: $this->order,
            sort: $this->sort,
            allowFallbacks: $this->allowFallbacks,
            requireParameters: $this->requireParameters,
            enforceDistillableText: $this->enforceDistillableText,
            zdr: $this->zdr,
            dataCollection: $this->dataCollection,
            only: $this->only,
            ignore: $this->ignore,
            preferredMinThroughput: $this->preferredMinThroughput,
            preferredMaxLatency: $this->preferredMaxLatency,
            maxPrice: $this->maxPrice,
            requireFeature: $this->requireFeature,
            quantizations: $quantizations,
        );
    }
}
