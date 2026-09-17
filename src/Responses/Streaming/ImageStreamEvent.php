<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Responses\Streaming;

/**
 * Interface ImageStreamEvent
 *
 * Common contract for all events received in the image streaming response.
 *
 * @package MichaelFrank\OpenRouter\Responses\Streaming
 */
interface ImageStreamEvent
{
    /**
     * Gets the type of the event.
     *
     * @return string
     */
    public function getType(): string;
}
