<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Enums\BatchStatus;
use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Responses\Batches\BatchDeletionResponse;
use MichaelFrank\OpenRouter\Responses\Batches\BatchListResponse;
use MichaelFrank\OpenRouter\Responses\Batches\BatchResponse;
use PHPUnit\Framework\TestCase;

final class BatchResponseTest extends TestCase
{
    public function testBatchResponseFromValidatingPayload(): void
    {
        $payload = [
            'id' => 'batch_123',
            'object' => 'batch',
            'endpoint' => '/v1/chat/completions',
            'model' => 'openai/gpt-4o',
            'completion_window' => '24h',
            'status' => 'validating',
            'created_at' => 1782097200,
            'finalized_at' => null,
            'request_counts' => [
                'total' => 1,
                'completed' => 0,
                'failed' => 0,
            ],
            'usage' => null,
            'results' => null,
            'error' => null,
        ];

        $metadata = new ResponseMetadata();
        $response = BatchResponse::fromArray($payload, $metadata);

        $this->assertSame('batch_123', $response->id);
        $this->assertSame('batch', $response->object);
        $this->assertSame('/v1/chat/completions', $response->endpoint);
        $this->assertSame('openai/gpt-4o', $response->model);
        $this->assertSame('24h', $response->completionWindow);
        $this->assertSame(BatchStatus::Validating, $response->status);
        $this->assertNotNull($response->createdAt);
        $this->assertSame(1782097200, $response->createdAt->getTimestamp());
        $this->assertNull($response->finalizedAt);
        $this->assertNotNull($response->requestCounts);
        $this->assertSame(1, $response->requestCounts->total);
        $this->assertSame(0, $response->requestCounts->completed);
        $this->assertSame(0, $response->requestCounts->failed);
        $this->assertNull($response->usage);
        $this->assertNull($response->results);
        $this->assertNull($response->error);
        $this->assertSame($metadata, $response->metadata);
    }

    public function testBatchResponseFromCompletedPayloadWithResults(): void
    {
        $payload = [
            'id' => 'batch_completed_456',
            'object' => 'batch',
            'endpoint' => '/v1/chat/completions',
            'model' => 'openai/gpt-4o',
            'completion_window' => '24h',
            'status' => 'completed',
            'created_at' => '2026-08-20T10:00:00Z',
            'finalized_at' => '2026-08-20T11:00:00Z',
            'request_counts' => [
                'total' => 2,
                'completed' => 2,
                'failed' => 0,
            ],
            'usage' => [
                'prompt_tokens' => 20,
                'completion_tokens' => 40,
                'total_tokens' => 60,
                'cost' => 0.000225,
                'is_byok' => false,
            ],
            'results' => [
                [
                    'id' => 'batch_req_1',
                    'custom_id' => 'req-0001',
                    'response' => [
                        'status_code' => 200,
                        'request_id' => 'req_abc',
                        'body' => [
                            'id' => 'gen-123',
                            'object' => 'chat.completion',
                            'choices' => [
                                [
                                    'index' => 0,
                                    'message' => [
                                        'role' => 'assistant',
                                        'content' => 'Test result',
                                    ],
                                    'finish_reason' => 'stop',
                                ],
                            ],
                        ],
                    ],
                    'error' => null,
                ],
                [
                    'id' => 'batch_req_2',
                    'custom_id' => 'req-0002',
                    'response' => null,
                    'error' => [
                        'message' => 'Validation error',
                        'code' => 400,
                    ],
                ],
            ],
            'error' => null,
        ];

        $metadata = new ResponseMetadata();
        $response = BatchResponse::fromArray($payload, $metadata);

        $this->assertSame(BatchStatus::Completed, $response->status);
        $this->assertNotNull($response->finalizedAt);
        $this->assertNotNull($response->usage);
        $this->assertSame(20, $response->usage->promptTokens);
        $this->assertSame(40, $response->usage->completionTokens);
        $this->assertSame(60, $response->usage->totalTokens);
        $this->assertSame(0.000225, $response->usage->cost);
        $this->assertFalse($response->usage->isByok);

        $this->assertNotNull($response->results);
        $this->assertCount(2, $response->results);

        $this->assertSame('batch_req_1', $response->results[0]->id);
        $this->assertSame('req-0001', $response->results[0]->customId);
        $this->assertNotNull($response->results[0]->response);
        $this->assertSame(200, $response->results[0]->response->statusCode);
        $this->assertSame('req_abc', $response->results[0]->response->requestId);
        $this->assertSame('gen-123', $response->results[0]->response->body['id']);

        $this->assertSame('batch_req_2', $response->results[1]->id);
        $this->assertSame('req-0002', $response->results[1]->customId);
        $this->assertNull($response->results[1]->response);
        $this->assertNotNull($response->results[1]->error);
        $this->assertSame('Validation error', $response->results[1]->error['message']);
    }

    public function testBatchResponseHandlesUnknownStatusFallback(): void
    {
        $payload = [
            'id' => 'batch_unknown',
            'object' => 'batch',
            'endpoint' => '/v1/chat/completions',
            'model' => 'openai/gpt-4o',
            'status' => 'future_unseen_status',
        ];

        $response = BatchResponse::fromArray($payload, new ResponseMetadata());
        $this->assertSame(BatchStatus::Unknown, $response->status);
    }

    public function testBatchListResponseFromPayload(): void
    {
        $payload = [
            'object' => 'list',
            'data' => [
                [
                    'id' => 'batch_1',
                    'object' => 'batch',
                    'endpoint' => '/v1/chat/completions',
                    'model' => 'openai/gpt-4o',
                    'completion_window' => '24h',
                    'status' => 'completed',
                ],
                [
                    'id' => 'batch_2',
                    'object' => 'batch',
                    'endpoint' => '/v1/embeddings',
                    'model' => 'openai/text-embedding-3-small',
                    'completion_window' => '24h',
                    'status' => 'in_progress',
                ],
            ],
            'first_id' => 'batch_1',
            'last_id' => 'batch_2',
            'has_more' => true,
        ];

        $metadata = new ResponseMetadata();
        $list = BatchListResponse::fromArray($payload, $metadata);

        $this->assertSame('list', $list->object);
        $this->assertCount(2, $list->data);
        $this->assertSame('batch_1', $list->firstId);
        $this->assertSame('batch_2', $list->lastId);
        $this->assertTrue($list->hasMore);
        $this->assertSame('batch_1', $list->data[0]->id);
        $this->assertSame(BatchStatus::Completed, $list->data[0]->status);
        $this->assertSame('batch_2', $list->data[1]->id);
        $this->assertSame(BatchStatus::InProgress, $list->data[1]->status);
    }

    public function testBatchDeletionResponseFromPayload(): void
    {
        $payload = [
            'id' => 'batch_del_123',
            'object' => 'batch',
            'deletion' => [
                'openrouter' => 'deleted',
                'upstream' => [
                    'provider' => 'Anthropic',
                    'status' => 'deleted',
                ],
            ],
        ];

        $metadata = new ResponseMetadata();
        $response = BatchDeletionResponse::fromArray($payload, $metadata);

        $this->assertSame('batch_del_123', $response->id);
        $this->assertSame('batch', $response->object);
        $this->assertSame('deleted', $response->deletion->openrouter);
        $this->assertNotNull($response->deletion->upstream);
        $this->assertSame('Anthropic', $response->deletion->upstream->provider);
        $this->assertSame('deleted', $response->deletion->upstream->status);
    }

    public function testBatchDeletionResponseWithoutUpstream(): void
    {
        $payload = [
            'id' => 'batch_del_456',
            'object' => 'batch',
            'deletion' => [
                'openrouter' => 'deleted',
            ],
        ];

        $metadata = new ResponseMetadata();
        $response = BatchDeletionResponse::fromArray($payload, $metadata);

        $this->assertSame('batch_del_456', $response->id);
        $this->assertSame('deleted', $response->deletion->openrouter);
        $this->assertNull($response->deletion->upstream);
    }
}
