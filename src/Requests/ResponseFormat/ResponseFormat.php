<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ResponseFormat;

/**
 * Interface ResponseFormat
 *
 * Configures the format constraints of models completions.
 *
 * @package MichaelFrank\OpenRouter\Requests\ResponseFormat
 */
interface ResponseFormat extends \JsonSerializable
{
    /**
     * Converts the response format configuration to a serializable array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
