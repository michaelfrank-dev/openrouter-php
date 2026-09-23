<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\Batches;

use MichaelFrank\OpenRouter\Enums\BatchStatus;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;

/**
 * Class BatchListQuery
 *
 * Configures pagination and filtering options when querying workspace batches.
 *
 * @package MichaelFrank\OpenRouter\Requests\Batches
 * @see https://openrouter.ai/docs/guides/overview/batch-api#list-your-batches
 */
final readonly class BatchListQuery implements \JsonSerializable
{
    /**
     * BatchListQuery constructor.
     *
     * @param int|null $limit Number of batches to return (1 to 100).
     * @param string|null $after Batch ID cursor to paginate after.
     * @param string|BatchStatus|array<int, string|BatchStatus>|null $status Single status or array of statuses.
     * @param int|string|\DateTimeInterface|null $createdAfter Timestamp or ISO-8601 datetime filter.
     * @param int|string|\DateTimeInterface|null $createdBefore Timestamp or ISO-8601 datetime filter.
     * @throws ValidationException
     */
    public function __construct(
        public ?int $limit = null,
        public ?string $after = null,
        public string|BatchStatus|array|null $status = null,
        public int|string|\DateTimeInterface|null $createdAfter = null,
        public int|string|\DateTimeInterface|null $createdBefore = null,
    ) {
        if ($this->limit !== null && ($this->limit < 1 || $this->limit > 100)) {
            throw new ValidationException('Batch list limit must be between 1 and 100.');
        }
    }

    /**
     * Formats a date filter into a string or timestamp representation.
     *
     * @param int|string|\DateTimeInterface $value
     * @return string
     */
    private function formatDateFilter(int|string|\DateTimeInterface $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTimeInterface::ATOM);
        }

        return (string) $value;
    }

    /**
     * Converts query parameters to an associative array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $params = [];

        if ($this->limit !== null) {
            $params['limit'] = (string) $this->limit;
        }

        if ($this->after !== null && trim($this->after) !== '') {
            $params['after'] = $this->after;
        }

        if ($this->status !== null) {
            if (is_array($this->status)) {
                $statuses = [];
                foreach ($this->status as $s) {
                    $statuses[] = $s instanceof BatchStatus ? $s->value : $s;
                }
                $params['status'] = $statuses;
            } else {
                $params['status'] = $this->status instanceof BatchStatus ? $this->status->value : $this->status;
            }
        }

        if ($this->createdAfter !== null) {
            $params['created_after'] = $this->formatDateFilter($this->createdAfter);
        }

        if ($this->createdBefore !== null) {
            $params['created_before'] = $this->formatDateFilter($this->createdBefore);
        }

        return $params;
    }

    /**
     * Serializes to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
