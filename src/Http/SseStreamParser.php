<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Http;

use MichaelFrank\OpenRouter\Exceptions\ApiResponseException;
use MichaelFrank\OpenRouter\Exceptions\NetworkException;
use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Responses\Streaming\CompletionStreamChunk;

/**
 * Class SseStreamParser
 *
 * Parses incoming server-sent event completion stream payloads.
 *
 * @package MichaelFrank\OpenRouter\Http
 */
final class SseStreamParser
{
    /**
     * Parses the streaming SSE response, yielding CompletionStreamChunk objects.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Generator<int, CompletionStreamChunk, mixed, null>
     * @throws NetworkException
     * @throws ApiResponseException
     */
    public function parse(\Psr\Http\Message\ResponseInterface $response): \Generator
    {
        $body = $response->getBody();
        $buffer = '';
        $metadata = ResponseMetadata::fromResponse($response);

        $contentType = strtolower($response->getHeaderLine('Content-Type'));
        $isJson = str_contains($contentType, 'application/json');

        if (!$isJson && !str_contains($contentType, 'event-stream')) {
            try {
                $peek = $body->read(1);
                if ($peek === '{' || $peek === '[') {
                    $isJson = true;
                }
                if ($peek !== '') {
                    $buffer .= $peek;
                }
            } catch (\Throwable) {
                // Ignore and proceed
            }
        }

        if ($isJson) {
            $bodyContent = $buffer . ((string)$body);
            try {
                $payload = json_decode($bodyContent, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new ApiResponseException(
                    'Malformed JSON response: ' . $e->getMessage(),
                    $bodyContent,
                    $metadata,
                    $e
                );
            }

            if (is_array($payload)) {
                if (array_key_exists('error', $payload)) {
                    $errorObj = $payload['error'];
                    $errorMessage = 'Unknown API error';

                    if (is_array($errorObj)) {
                        $errorMessage = (string)($errorObj['message'] ?? $errorObj['code'] ?? 'Unknown API error');
                    } elseif (is_string($errorObj)) {
                        $errorMessage = $errorObj;
                    }

                    throw new ApiResponseException($errorMessage, $bodyContent, $metadata);
                }
            }

            throw new ApiResponseException(
                'Expected event stream response (text/event-stream), but received application/json. The selected model or provider does not support streaming completions.',
                $bodyContent,
                $metadata
            );
        }

        while (!$body->eof()) {
            try {
                $chunk = $body->read(4096);
            } catch (\Throwable $e) {
                throw new NetworkException(
                    'Network error reading completion stream: ' . $e->getMessage(),
                    $metadata,
                    $e
                );
            }

            if ($chunk === '') {
                usleep(1000);
                continue;
            }

            $buffer .= $chunk;

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 1);

                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                // Ignore comment lines (lines starting with colon)
                if (str_starts_with($line, ':')) {
                    continue;
                }

                if (!str_starts_with($line, 'data:')) {
                    continue;
                }

                $data = trim(substr($line, 5));

                if ($data === '[DONE]') {
                    return null;
                }

                try {
                    $payload = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    throw new ApiResponseException(
                        'Malformed JSON chunk received in stream: ' . $e->getMessage(),
                        $data,
                        $metadata,
                        $e
                    );
                }

                if (is_array($payload)) {
                    if (array_key_exists('error', $payload)) {
                        $errorObj = $payload['error'];
                        $errorMessage = 'Unknown stream error';

                        if (is_array($errorObj)) {
                            $errorMessage = (string)($errorObj['message'] ?? $errorObj['code'] ?? 'Unknown stream error');
                        } elseif (is_string($errorObj)) {
                            $errorMessage = $errorObj;
                        }

                        throw new ApiResponseException($errorMessage, $data, $metadata);
                    }

                    yield CompletionStreamChunk::fromArray($payload, $metadata);
                }
            }
        }

        $buffer = trim($buffer);
        if ($buffer !== '' && str_starts_with($buffer, 'data:')) {
            $data = trim(substr($buffer, 5));
            if ($data !== '[DONE]') {
                try {
                    $payload = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
                    if (is_array($payload)) {
                        if (array_key_exists('error', $payload)) {
                            $errorObj = $payload['error'];
                            $errorMessage = 'Unknown stream error';

                            if (is_array($errorObj)) {
                                $errorMessage = (string)($errorObj['message'] ?? $errorObj['code'] ?? 'Unknown stream error');
                            } elseif (is_string($errorObj)) {
                                $errorMessage = $errorObj;
                            }

                            throw new ApiResponseException($errorMessage, $data, $metadata);
                        }

                        yield CompletionStreamChunk::fromArray($payload, $metadata);
                    }
                } catch (\JsonException $e) {
                    throw new ApiResponseException(
                        'Malformed JSON chunk received in stream suffix: ' . $e->getMessage(),
                        $data,
                        $metadata,
                        $e
                    );
                }
            }
        }

        return null;
    }
}
