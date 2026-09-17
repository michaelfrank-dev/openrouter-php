<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Http;

use MichaelFrank\OpenRouter\Exceptions\ApiResponseException;
use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;

/**
 * Class JsonResponseValidator
 *
 * Decodes JSON response bodies and checks for API in-body error messages.
 * Does not check HTTP status codes.
 *
 * @package MichaelFrank\OpenRouter\Http
 */
final class JsonResponseValidator
{
    /**
     * Decodes a JSON response, validates its structure, and checks for API-level errors.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array<string, mixed>
     * @throws ApiResponseException
     */
    public function decode(\Psr\Http\Message\ResponseInterface $response): array
    {
        $body = (string)$response->getBody();

        try {
            $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $metadata = ResponseMetadata::fromResponse($response);
            throw new ApiResponseException(
                'Failed to decode JSON response: ' . $e->getMessage(),
                $body,
                $metadata,
                $e
            );
        }

        if (!is_array($data)) {
            $metadata = ResponseMetadata::fromResponse($response);
            throw new ApiResponseException(
                'API returned an invalid JSON response structure.',
                $body,
                $metadata
            );
        }

        if (array_key_exists('error', $data)) {
            $metadata = ResponseMetadata::fromResponse($response);
            $errorObj = $data['error'];
            $message = 'API Error';

            if (is_array($errorObj)) {
                $message = (string)($errorObj['message'] ?? $errorObj['code'] ?? 'Unknown API Error');
            } elseif (is_string($errorObj)) {
                $message = $errorObj;
            }

            throw new ApiResponseException($message, $body, $metadata);
        }

        $result = [];
        foreach ($data as $k => $v) {
            $result[(string)$k] = $v;
        }

        return $result;
    }
}
