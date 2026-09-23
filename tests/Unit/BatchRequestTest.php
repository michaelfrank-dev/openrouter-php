<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Enums\BatchStatus;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use MichaelFrank\OpenRouter\Requests\Batches\BatchCreateRequest;
use MichaelFrank\OpenRouter\Requests\Batches\BatchEndpoint;
use MichaelFrank\OpenRouter\Requests\Batches\BatchItem;
use MichaelFrank\OpenRouter\Requests\Batches\BatchListQuery;
use MichaelFrank\OpenRouter\Requests\Batches\BatchProviderRouting;
use PHPUnit\Framework\TestCase;

final class BatchRequestTest extends TestCase
{
    public function testBatchEndpointConstantsAndFromString(): void
    {
        $endpoint = BatchEndpoint::fromString(BatchEndpoint::CHAT_COMPLETIONS);
        $this->assertSame('/v1/chat/completions', $endpoint->value);

        $custom = BatchEndpoint::fromString('/v1/custom');
        $this->assertSame('/v1/custom', $custom->value);
    }

    public function testBatchItemValidAndSerialization(): void
    {
        $item = new BatchItem(
            customId: 'req-001',
            body: ['messages' => [['role' => 'user', 'content' => 'Hello']]]
        );

        $expected = [
            'custom_id' => 'req-001',
            'body' => ['messages' => [['role' => 'user', 'content' => 'Hello']]],
        ];

        $this->assertSame($expected, $item->toArray());
        $this->assertSame($expected, $item->jsonSerialize());
    }

    public function testBatchItemThrowsOnEmptyCustomId(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Batch item customId cannot be empty.');

        new BatchItem(customId: '   ', body: ['foo' => 'bar']);
    }

    public function testBatchProviderRoutingValidAndSerialization(): void
    {
        $routing = new BatchProviderRouting(['google-vertex', 'together']);
        $expected = ['only' => ['google-vertex', 'together']];

        $this->assertSame($expected, $routing->toArray());
        $this->assertSame($expected, $routing->jsonSerialize());
    }

    public function testBatchProviderRoutingThrowsOnEmptyArray(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Batch provider routing "only" list cannot be empty.');

        new BatchProviderRouting([]);
    }

    public function testBatchProviderRoutingThrowsOnInvalidSlug(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Provider slug in "only" list must be a non-empty string.');

        new BatchProviderRouting(['']);
    }

    public function testBatchCreateRequestValidAndKeyOrdering(): void
    {
        $items = [
            new BatchItem('req-1', ['messages' => [['role' => 'user', 'content' => 'Hi']]]),
            new BatchItem('req-2', ['messages' => [['role' => 'user', 'content' => 'Bye']]]),
        ];
        $provider = new BatchProviderRouting(['google-vertex']);

        $request = new BatchCreateRequest(
            endpoint: BatchEndpoint::fromString(BatchEndpoint::CHAT_COMPLETIONS),
            model: 'openai/gpt-4o',
            requests: $items,
            provider: $provider,
            completionWindow: '24h'
        );

        $array = $request->toArray();
        $keys = array_keys($array);

        $this->assertSame(['endpoint', 'model', 'completion_window', 'provider', 'requests'], $keys);
        $this->assertSame('/v1/chat/completions', $array['endpoint']);
        $this->assertSame('openai/gpt-4o', $array['model']);
        $this->assertSame('24h', $array['completion_window']);
        $this->assertSame(['only' => ['google-vertex']], $array['provider']);
        $requests = $array['requests'];
        $this->assertIsArray($requests);
        $this->assertCount(2, $requests);
        $first = $requests[0];
        $this->assertIsArray($first);
        $this->assertSame('req-1', $first['custom_id']);
    }

    public function testBatchCreateRequestThrowsOnEmptyEndpoint(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Batch endpoint cannot be empty.');

        new BatchCreateRequest(
            endpoint: '',
            model: 'openai/gpt-4o',
            requests: [new BatchItem('req-1', ['input' => 'test'])]
        );
    }

    public function testBatchCreateRequestThrowsOnEmptyModel(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Batch model cannot be empty.');

        new BatchCreateRequest(
            endpoint: BatchEndpoint::CHAT_COMPLETIONS,
            model: '   ',
            requests: [new BatchItem('req-1', ['input' => 'test'])]
        );
    }

    public function testBatchCreateRequestThrowsOnEmptyRequests(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Batch requests array cannot be empty.');

        new BatchCreateRequest(
            endpoint: BatchEndpoint::CHAT_COMPLETIONS,
            model: 'openai/gpt-4o',
            requests: []
        );
    }

    public function testBatchCreateRequestThrowsOnInvalidItemType(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Item at index 0 must be an instance of');

        /** @psalm-suppress InvalidArgument */
        new BatchCreateRequest(
            endpoint: BatchEndpoint::CHAT_COMPLETIONS,
            model: 'openai/gpt-4o',
            // @phpstan-ignore-next-line
            requests: [['custom_id' => 'req-1', 'body' => []]]
        );
    }

    public function testBatchListQueryValidAndSerialization(): void
    {
        $date = new \DateTimeImmutable('2026-08-20T00:00:00+00:00');
        $query = new BatchListQuery(
            limit: 50,
            after: 'batch_123',
            status: [BatchStatus::Completed, BatchStatus::Failed],
            createdAfter: 1787184000,
            createdBefore: $date
        );

        $array = $query->toArray();

        $this->assertSame('50', $array['limit']);
        $this->assertSame('batch_123', $array['after']);
        $this->assertSame(['completed', 'failed'], $array['status']);
        $this->assertSame('1787184000', $array['created_after']);
        $this->assertSame($date->format(\DateTimeInterface::ATOM), $array['created_before']);
    }

    public function testBatchListQueryThrowsOnInvalidLimit(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Batch list limit must be between 1 and 100.');

        new BatchListQuery(limit: 0);
    }

    public function testBatchListQueryThrowsOnLimitAbove100(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Batch list limit must be between 1 and 100.');

        new BatchListQuery(limit: 101);
    }
}
