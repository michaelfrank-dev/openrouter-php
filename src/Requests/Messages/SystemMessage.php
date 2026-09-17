<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Messages;

/**
 * Class SystemMessage
 *
 * Represents a system instruction prompt.
 *
 * @package MichaelFrank\OpenRouter\Requests\Messages
 */
final readonly class SystemMessage implements MessageInterface
{
    /**
     * SystemMessage constructor.
     *
     * @param string $content
     * @param string|null $name
     */
    public function __construct(
        public string $content,
        public ?string $name = null,
    ) {
    }

    /**
     * Converts to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'role' => 'system',
            'content' => $this->content,
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        return $data;
    }

    /**
     * JSON serialization format.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
