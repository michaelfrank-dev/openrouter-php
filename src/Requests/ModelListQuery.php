<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

/**
 * Class ModelListQuery
 *
 * Configures sorting and filter options when requesting the models list.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final readonly class ModelListQuery implements \JsonSerializable
{
    /**
     * ModelListQuery constructor.
     *
     * @param ModelListSort|null $sort
     * @param string|null $category
     * @param string|null $outputModalities
     * @param string|null $supportedParameters
     * @param string|null $search
     * @param string|null $cursor
     */
    public function __construct(
        public ?ModelListSort $sort = null,
        public ?string $category = null,
        public ?string $outputModalities = null,
        public ?string $supportedParameters = null,
        public ?string $search = null,
        public ?string $cursor = null,
    ) {
    }

    /**
     * Converts to query array (for url query params or serialization).
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->sort !== null) {
            $data['sort'] = $this->sort->value;
        }
        if ($this->category !== null) {
            $data['category'] = $this->category;
        }
        if ($this->outputModalities !== null) {
            $data['output_modalities'] = $this->outputModalities;
        }
        if ($this->supportedParameters !== null) {
            $data['supported_parameters'] = $this->supportedParameters;
        }
        if ($this->search !== null) {
            $data['search'] = $this->search;
        }
        if ($this->cursor !== null) {
            $data['cursor'] = $this->cursor;
        }

        return $data;
    }

    /**
     * JSON serialization format.
     *
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
