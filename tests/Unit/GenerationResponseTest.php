<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MichaelFrank\OpenRouter\Responses\GenerationResponse;
use MichaelFrank\OpenRouter\Responses\GenerationData;
use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Metadata\RateLimit;

final class GenerationResponseTest extends TestCase
{
    public function testInstantiationAndMetadata(): void
    {
        $metadata = new ResponseMetadata(
            requestId: 'test-id',
            rateLimit: new RateLimit(100, 99, null)
        );

        $response = new GenerationResponse(
            data: [],
            metadata: $metadata
        );

        $this->assertSame($metadata, $response->metadata);
        $this->assertEmpty($response->data);
    }

    public function testFromArrayWithNullMetadataFallback(): void
    {
        $payload = [
            'data' => [
                'id' => 'gen-123',
                'model' => 'test-model',
                'native_tokens' => 10,
            ]
        ];

        $response = GenerationResponse::fromArray($payload);

        $this->assertNull($response->metadata->requestId);
        $this->assertInstanceOf(RateLimit::class, $response->metadata->rateLimit);
    }

    public function testFromArrayWithMetadataPassed(): void
    {
        $payload = [
            'data' => [
                [
                    'id' => 'gen-123',
                    'model' => 'test-model',
                    'native_tokens' => 10,
                ]
            ]
        ];

        $metadata = new ResponseMetadata(
            requestId: 'req-abc',
            rateLimit: new RateLimit(50, 48, null)
        );

        $response = GenerationResponse::fromArray($payload, $metadata);

        $this->assertSame($metadata, $response->metadata);
        $this->assertCount(1, $response->data);
        $this->assertInstanceOf(GenerationData::class, $response->data[0]);
    }
}
