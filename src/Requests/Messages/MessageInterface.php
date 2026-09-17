<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Messages;

/**
 * Interface MessageInterface
 *
 * Represents a single message in a chat completion request.
 *
 * @package MichaelFrank\OpenRouter\Requests\Messages
 */
interface MessageInterface extends \JsonSerializable
{
    /**
     * Converts the message to a serializable array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
