<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

use MichaelFrank\OpenRouter\Exceptions\ValidationException;

/**
 * Class XSearchFilter
 *
 * Configures X/Twitter search filters for xAI models.
 *
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class XSearchFilter implements \JsonSerializable
{
    /**
     * XSearchFilter constructor.
     *
     * @param array<string>|null $allowedXHandles Only include posts from these handles (max 10).
     * @param array<string>|null $excludedXHandles Exclude posts from these handles (max 10).
     * @param string|null $fromDate Start date for search range (ISO 8601, e.g. "2025-01-01").
     * @param string|null $toDate End date for search range (ISO 8601, e.g. "2025-12-31").
     * @param bool|null $enableImageUnderstanding Enable analysis of images within posts.
     * @param bool|null $enableVideoUnderstanding Enable analysis of videos within posts.
     * @throws ValidationException
     */
    public function __construct(
        public ?array $allowedXHandles = null,
        public ?array $excludedXHandles = null,
        public ?string $fromDate = null,
        public ?string $toDate = null,
        public ?bool $enableImageUnderstanding = null,
        public ?bool $enableVideoUnderstanding = null,
    ) {
        if ($this->allowedXHandles !== null && $this->excludedXHandles !== null) {
            throw new ValidationException('allowed_x_handles and excluded_x_handles are mutually exclusive.');
        }

        if ($this->allowedXHandles !== null && count($this->allowedXHandles) > 10) {
            throw new ValidationException('allowed_x_handles cannot exceed 10 items.');
        }

        if ($this->excludedXHandles !== null && count($this->excludedXHandles) > 10) {
            throw new ValidationException('excluded_x_handles cannot exceed 10 items.');
        }
    }

    /**
     * Converts to payload array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->allowedXHandles !== null) {
            $data['allowed_x_handles'] = $this->allowedXHandles;
        }

        if ($this->excludedXHandles !== null) {
            $data['excluded_x_handles'] = $this->excludedXHandles;
        }

        if ($this->fromDate !== null) {
            $data['from_date'] = $this->fromDate;
        }

        if ($this->toDate !== null) {
            $data['to_date'] = $this->toDate;
        }

        if ($this->enableImageUnderstanding !== null) {
            $data['enable_image_understanding'] = $this->enableImageUnderstanding;
        }

        if ($this->enableVideoUnderstanding !== null) {
            $data['enable_video_understanding'] = $this->enableVideoUnderstanding;
        }

        return $data;
    }

    /**
     * Creates an empty XSearchFilter instance with no filters, enabling X Search.
     *
     * @return self
     */
    public static function enabled(): self
    {
        return new self();
    }

    /**
     * JSON serialization format.
     *
     * @return array<string, mixed>|\stdClass
     */
    public function jsonSerialize(): array|\stdClass
    {
        $data = $this->toArray();

        return $data === [] ? new \stdClass() : $data;
    }
}
