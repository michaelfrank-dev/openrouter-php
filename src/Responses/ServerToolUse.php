<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses;

/**
 * Class ServerToolUse
 *
 * Keeps track of server-side agentic tools usage stats.
 *
 * @package MichaelFrank\OpenRouter\Responses
 */
final readonly class ServerToolUse
{
    /**
     * ServerToolUse constructor.
     *
     * @param int|null $webSearchRequests Number of web searches performed by server-side tools
     * @param int|null $toolCallsExecuted Number of OpenRouter server tool calls that executed and produced a result
     * @param int|null $toolCallsRequested Total number of OpenRouter server-orchestrated tool calls requested
     */
    public function __construct(
        public ?int $webSearchRequests = null,
        public ?int $toolCallsExecuted = null,
        public ?int $toolCallsRequested = null,
    ) {
    }

    /**
     * Factory to build ServerToolUse from payload array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $requests = $data['web_search_requests'] ?? null;
        $executed = $data['tool_calls_executed'] ?? null;
        $requested = $data['tool_calls_requested'] ?? null;

        return new self(
            webSearchRequests: is_numeric($requests) ? (int)$requests : null,
            toolCallsExecuted: is_numeric($executed) ? (int)$executed : null,
            toolCallsRequested: is_numeric($requested) ? (int)$requested : null
        );
    }
}
