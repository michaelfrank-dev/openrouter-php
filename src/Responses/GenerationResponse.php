<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Metadata\RateLimit;

/**
 * Class GenerationResponse
 *
 * Holds lists of generation stats returned by OpenRouter stats querying.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class GenerationResponse
{
    /**
     * GenerationResponse constructor.
     *
     * @param array<GenerationData> $data
     * @param ResponseMetadata $metadata
     */
    public function __construct(
        public array $data,
        public ResponseMetadata $metadata,
    ) {
    }

    /**
     * Factory to build GenerationResponse from payload array.
     *
     * @param array<mixed> $data
     * @param ResponseMetadata|null $metadata
     * @return self
     */
    public static function fromArray(array $data, ?ResponseMetadata $metadata = null): self
    {
        $infos = [];
        $dataVal = $data['data'] ?? null;

        if (is_array($dataVal)) {
            $isList = true;
            foreach (array_keys($dataVal) as $k) {
                if (!is_int($k)) {
                    $isList = false;
                    break;
                }
            }
            if ($isList) {
                foreach ($dataVal as $item) {
                    if (is_array($item)) {
                        $infos[] = GenerationData::fromArray($item);
                    }
                }
            } else {
                $infos[] = GenerationData::fromArray($dataVal);
            }
        } elseif (array_is_list($data)) {
            foreach ($data as $item) {
                if (is_array($item)) {
                    $infos[] = GenerationData::fromArray($item);
                }
            }
        } else {
            $infos[] = GenerationData::fromArray($data);
        }

        return new self(
            $infos,
            $metadata ?? new ResponseMetadata()
        );
    }
}
