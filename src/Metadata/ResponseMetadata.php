<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Metadata;

/**
 * Class ResponseMetadata
 *
 * Holds metadata context for a finished OpenRouter request.
 *
 * @package MichaelFrank\OpenRouter\Metadata
 */
final readonly class ResponseMetadata
{
    /**
     * ResponseMetadata constructor.
     *
     * @param string|null $requestId
     * @param RateLimit $rateLimit
     */
    public function __construct(
        public ?string $requestId = null,
        public RateLimit $rateLimit = new RateLimit(null, null, null),
    ) {
    }

    /**
     * Extracts response metadata from PSR-7 response headers.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return self
     */
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response): self
    {
        // Check for 'X-Generation-Id' header
        $requestId = null;
        if ($response->hasHeader('x-generation-id')) {
            $requestId = $response->getHeaderLine('x-generation-id');
        }

        // Parse rate limit headers
        $rateLimit = RateLimit::fromResponse($response);

        return new self($requestId, $rateLimit);
    }
}
