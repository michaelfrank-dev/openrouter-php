<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Metadata;

/**
 * Class RateLimit
 *
 * Holds rate limiting information returned in OpenRouter API headers.
 *
 * @package MichaelFrank\OpenRouter\Metadata
 */
final readonly class RateLimit
{
    /**
     * RateLimit constructor.
     *
     * @param int|null $limit
     * @param int|null $remaining
     * @param \DateTimeImmutable|null $resetAt
     */
    public function __construct(
        public ?int $limit,
        public ?int $remaining,
        public ?\DateTimeImmutable $resetAt,
    ) {
    }

    /**
     * Parses standard rate limiting headers from a PSR-7 response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return self
     */
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response): self
    {
        $limit = null;
        $remaining = null;
        $resetAt = null;

        if ($response->hasHeader('x-ratelimit-limit')) {
            $limit = (int) $response->getHeaderLine('x-ratelimit-limit');
        }
        if ($response->hasHeader('x-ratelimit-remaining')) {
            $remaining = (int) $response->getHeaderLine('x-ratelimit-remaining');
        }

        if ($response->hasHeader('x-ratelimit-reset')) {
            $resetVal = $response->getHeaderLine('x-ratelimit-reset');
            if (is_numeric($resetVal)) {
                $val = (float) $resetVal;
                // If it's a relative offset in seconds (e.g. less than 1 year)
                if ($val < 31536000) {
                    $resetAt = new \DateTimeImmutable('+' . (int)$val . ' seconds');
                } else {
                    $resetAt = new \DateTimeImmutable('@' . (int)$val);
                }
            }
        } elseif ($response->hasHeader('retry-after')) {
            $retryAfter = $response->getHeaderLine('retry-after');
            if (is_numeric($retryAfter)) {
                $resetAt = new \DateTimeImmutable('+' . (int)$retryAfter . ' seconds');
            } else {
                try {
                    $resetAt = new \DateTimeImmutable($retryAfter);
                } catch (\Exception) {
                    // Ignore date parsing exceptions gracefully
                }
            }
        }

        return new self($limit, $remaining, $resetAt);
    }

    /**
     * Calculates the remaining seconds to wait until rate limit resets.
     *
     * @return int|null
     */
    public function getRetryAfterSeconds(): ?int
    {
        if ($this->resetAt === null) {
            return null;
        }
        $diff = $this->resetAt->getTimestamp() - time();
        return $diff > 0 ? $diff : 0;
    }
}
