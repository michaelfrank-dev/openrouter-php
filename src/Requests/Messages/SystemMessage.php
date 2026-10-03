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
     * @param array<string, mixed>|null $configurationUpdate Mid-conversation configuration update (e.g. reasoning effort changes).
     */
    public function __construct(
        public string $content,
        public ?string $name = null,
        public ?array $configurationUpdate = null,
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

        if ($this->configurationUpdate !== null && $this->configurationUpdate !== []) {
            $data['configuration_update'] = $this->configurationUpdate;
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
