<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use PHPUnit\Framework\TestCase;

final class ResponseMetadataTest extends TestCase
{
    public function testFromResponseWithXGenerationId(): void
    {
        $response = new Response(200, [
            'x-generation-id' => 'gen-789',
            'x-ratelimit-limit' => '100',
            'x-ratelimit-remaining' => '99',
        ]);

        $metadata = ResponseMetadata::fromResponse($response);

        $this->assertEquals('gen-789', $metadata->requestId);
        $this->assertEquals(100, $metadata->rateLimit->limit);
        $this->assertEquals(99, $metadata->rateLimit->remaining);
    }

    public function testFromResponseWithoutXGenerationId(): void
    {
        $response = new Response(200, [
            'x-ratelimit-limit' => '50',
            'x-ratelimit-remaining' => '45',
        ]);

        $metadata = ResponseMetadata::fromResponse($response);

        $this->assertNull($metadata->requestId);
        $this->assertEquals(50, $metadata->rateLimit->limit);
        $this->assertEquals(45, $metadata->rateLimit->remaining);
    }
}
