<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

use MichaelFrank\OpenRouter\Exceptions\ApiResponseException;

/**
 * Class FunctionCall
 *
 * Represents the name and JSON string arguments of a function call triggered by a model.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class FunctionCall
{
    /**
     * FunctionCall constructor.
     *
     * @param string $name
     * @param string $arguments JSON encoded string of arguments.
     */
    public function __construct(
        public string $name,
        public string $arguments,
    ) {
    }

    /**
     * Factory to build FunctionCall from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $name = $data['name'] ?? null;
        $args = $data['arguments'] ?? null;

        return new self(
            name: is_string($name) ? $name : '',
            arguments: is_string($args) ? $args : ''
        );
    }

    /**
     * Helper to safely decode function arguments into a PHP array.
     *
     * @return array<string, mixed>
     * @throws ApiResponseException
     */
    public function parseArguments(): array
    {
        try {
            $decoded = json_decode($this->arguments, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($decoded)) {
                throw new \JsonException('Function call arguments did not decode to a JSON object.');
            }
            $result = [];
            foreach ($decoded as $k => $v) {
                $result[(string)$k] = $v;
            }
            return $result;
        } catch (\JsonException $e) {
            throw new ApiResponseException(
                'Failed to parse function call arguments as JSON: ' . $e->getMessage(),
                $this->arguments
            );
        }
    }
}
