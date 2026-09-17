<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Responses\ImageGenerationResponse;
use MichaelFrank\OpenRouter\Responses\ImageStreamingResponse;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageGenCompletedEvent;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageGenPartialImageEvent;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageGenStreamErrorEvent;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageGenTextChunkEvent;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageGenerationResponseTest
 *
 * Tests the Response DTO factories for image generation.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class ImageGenerationResponseTest extends TestCase
{
    public function testImageGenerationResponseFromArray(): void
    {
        $payload = [
            'created' => 1748372400,
            'data' => [
                [
                    'b64_json' => 'encoded_image_data_1',
                    'media_type' => 'image/png'
                ],
                [
                    'b64_json' => 'encoded_image_data_2'
                ]
            ],
            'usage' => [
                'prompt_tokens' => 0,
                'completion_tokens' => 4175,
                'total_tokens' => 4175,
                'cost' => 0.04
            ]
        ];

        $metadata = new ResponseMetadata();
        $response = ImageGenerationResponse::fromArray($payload, $metadata);

        $this->assertEquals(1748372400, $response->created);
        $this->assertCount(2, $response->data);
        $this->assertEquals('encoded_image_data_1', $response->data[0]->b64Json);
        $this->assertEquals('image/png', $response->data[0]->mediaType);
        $this->assertEquals('encoded_image_data_2', $response->data[1]->b64Json);
        $this->assertNull($response->data[1]->mediaType);

        $this->assertNotNull($response->usage);
        $this->assertEquals(0, $response->usage->promptTokens);
        $this->assertEquals(4175, $response->usage->completionTokens);
        $this->assertEquals(4175, $response->usage->totalTokens);
        $this->assertEquals(0.04, $response->usage->cost);
        $this->assertSame($metadata, $response->metadata);
    }

    public function testImageStreamingResponsePartialImage(): void
    {
        $payload = [
            'data' => [
                'type' => 'image_generation.partial_image',
                'b64_json' => 'partial_bytes',
                'partial_image_index' => 3
            ]
        ];

        $metadata = new ResponseMetadata();
        $streamResponse = ImageStreamingResponse::fromArray($payload, $metadata);

        $this->assertInstanceOf(ImageGenPartialImageEvent::class, $streamResponse->data);
        $this->assertEquals('image_generation.partial_image', $streamResponse->data->getType());
        $this->assertEquals('partial_bytes', $streamResponse->data->b64Json);
        $this->assertEquals(3, $streamResponse->data->partialImageIndex);
        $this->assertSame($metadata, $streamResponse->metadata);
    }

    public function testImageStreamingResponseTextChunk(): void
    {
        $payload = [
            'data' => [
                'type' => 'image_generation.text_chunk',
                'text' => '<svg>',
                'phase' => 'content'
            ]
        ];

        $metadata = new ResponseMetadata();
        $streamResponse = ImageStreamingResponse::fromArray($payload, $metadata);

        $this->assertInstanceOf(ImageGenTextChunkEvent::class, $streamResponse->data);
        $this->assertEquals('image_generation.text_chunk', $streamResponse->data->getType());
        $this->assertEquals('<svg>', $streamResponse->data->text);
        $this->assertEquals('content', $streamResponse->data->phase);
    }

    public function testImageStreamingResponseCompleted(): void
    {
        $payload = [
            'data' => [
                'type' => 'image_generation.completed',
                'b64_json' => 'final_bytes',
                'created' => 1748372400,
                'media_type' => 'image/png',
                'usage' => [
                    'prompt_tokens' => 0,
                    'completion_tokens' => 10,
                    'total_tokens' => 10
                ]
            ]
        ];

        $metadata = new ResponseMetadata();
        $streamResponse = ImageStreamingResponse::fromArray($payload, $metadata);

        $this->assertInstanceOf(ImageGenCompletedEvent::class, $streamResponse->data);
        $this->assertEquals('image_generation.completed', $streamResponse->data->getType());
        $this->assertEquals('final_bytes', $streamResponse->data->b64Json);
        $this->assertEquals(1748372400, $streamResponse->data->created);
        $this->assertEquals('image/png', $streamResponse->data->mediaType);
        $this->assertNotNull($streamResponse->data->usage);
        $this->assertEquals(10, $streamResponse->data->usage->totalTokens);
    }

    public function testImageStreamingResponseError(): void
    {
        $payload = [
            'data' => [
                'type' => 'error',
                'error' => [
                    'code' => 'provider_timeout',
                    'message' => 'The provider timed out.'
                ]
            ]
        ];

        $metadata = new ResponseMetadata();
        $streamResponse = ImageStreamingResponse::fromArray($payload, $metadata);

        $this->assertInstanceOf(ImageGenStreamErrorEvent::class, $streamResponse->data);
        $this->assertEquals('error', $streamResponse->data->getType());
        $this->assertEquals([
            'code' => 'provider_timeout',
            'message' => 'The provider timed out.'
        ], $streamResponse->data->error);
    }

    public function testImageStreamingResponseFlatCompleted(): void
    {
        $payload = [
            'type' => 'image_generation.completed',
            'b64_json' => 'final_bytes_flat',
            'created' => 1748372400,
            'media_type' => 'image/png',
            'usage' => [
                'prompt_tokens' => 0,
                'completion_tokens' => 10,
                'total_tokens' => 10
            ]
        ];

        $metadata = new ResponseMetadata();
        $streamResponse = ImageStreamingResponse::fromArray($payload, $metadata);

        $this->assertInstanceOf(ImageGenCompletedEvent::class, $streamResponse->data);
        $this->assertEquals('image_generation.completed', $streamResponse->data->getType());
        $this->assertEquals('final_bytes_flat', $streamResponse->data->b64Json);
        $this->assertEquals(1748372400, $streamResponse->data->created);
        $this->assertEquals('image/png', $streamResponse->data->mediaType);
        $this->assertNotNull($streamResponse->data->usage);
        $this->assertEquals(10, $streamResponse->data->usage->totalTokens);
    }
}
