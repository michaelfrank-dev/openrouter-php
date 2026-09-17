<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use MichaelFrank\OpenRouter\Exceptions\ApiResponseException;
use MichaelFrank\OpenRouter\Http\ImageSseStreamParser;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageGenCompletedEvent;
use MichaelFrank\OpenRouter\Responses\Streaming\ImageGenPartialImageEvent;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageSseStreamParserTest
 *
 * Tests the ImageSseStreamParser logic for reading events and handling exceptions.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class ImageSseStreamParserTest extends TestCase
{
    public function testParseStreamEvents(): void
    {
        $event1 = [
            'data' => [
                'type' => 'image_generation.partial_image',
                'b64_json' => 'partial_bytes',
                'partial_image_index' => 0
            ]
        ];
        $event2 = [
            'data' => [
                'type' => 'image_generation.completed',
                'b64_json' => 'completed_bytes',
                'created' => 1748372400
            ]
        ];

        $streamContent = "data: " . json_encode($event1) . "\n\n" .
                         ": comment line\n" .
                         "data: " . json_encode($event2) . "\n\n" .
                         "data: [DONE]\n";

        $response = new Response(200, [], Utils::streamFor($streamContent));
        $parser = new ImageSseStreamParser();
        $generator = $parser->parse($response);

        $results = iterator_to_array($generator);

        $this->assertCount(2, $results);

        $this->assertInstanceOf(ImageGenPartialImageEvent::class, $results[0]->data);
        $this->assertEquals('partial_bytes', $results[0]->data->b64Json);

        $this->assertInstanceOf(ImageGenCompletedEvent::class, $results[1]->data);
        $this->assertEquals('completed_bytes', $results[1]->data->b64Json);
    }

    public function testStreamErrorEventThrowsException(): void
    {
        $errorEvent = [
            'data' => [
                'type' => 'error',
                'error' => [
                    'message' => 'Upstream service overloaded'
                ]
            ]
        ];

        $streamContent = "data: " . json_encode($errorEvent) . "\n";
        $response = new Response(200, [], Utils::streamFor($streamContent));
        $parser = new ImageSseStreamParser();

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Upstream service overloaded');

        iterator_to_array($parser->parse($response));
    }

    public function testStreamRootErrorThrowsException(): void
    {
        $rootError = [
            'error' => [
                'message' => 'Unauthorized key'
            ]
        ];

        $streamContent = "data: " . json_encode($rootError) . "\n";
        $response = new Response(200, [], Utils::streamFor($streamContent));
        $parser = new ImageSseStreamParser();

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Unauthorized key');

        iterator_to_array($parser->parse($response));
    }

    public function testParseNonSseJsonResponseThrowsException(): void
    {
        $payload = [
            'created' => 1748372400,
            'data' => [
                ['b64_json' => 'completed_bytes']
            ]
        ];

        $response = new Response(200, ['Content-Type' => 'application/json'], Utils::streamFor(json_encode($payload)));
        $parser = new ImageSseStreamParser();

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('The selected model or provider does not support streaming image generation');

        iterator_to_array($parser->parse($response));
    }

    public function testParseNonSseJsonErrorResponseThrowsException(): void
    {
        $payload = [
            'error' => [
                'message' => 'Billing quota exceeded'
            ]
        ];

        $response = new Response(200, ['Content-Type' => 'application/json'], Utils::streamFor(json_encode($payload)));
        $parser = new ImageSseStreamParser();

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Billing quota exceeded');

        iterator_to_array($parser->parse($response));
    }

    public function testParseFlatStreamEvents(): void
    {
        $event1 = [
            'type' => 'image_generation.partial_image',
            'b64_json' => 'partial_bytes_flat',
            'partial_image_index' => 0
        ];
        $event2 = [
            'type' => 'image_generation.completed',
            'b64_json' => 'completed_bytes_flat',
            'created' => 1748372400
        ];

        $streamContent = "data: " . json_encode($event1) . "\n\n" .
                         ": comment line\n" .
                         "data: " . json_encode($event2) . "\n\n" .
                         "data: [DONE]\n";

        $response = new Response(200, [], Utils::streamFor($streamContent));
        $parser = new ImageSseStreamParser();
        $generator = $parser->parse($response);

        $results = iterator_to_array($generator);

        $this->assertCount(2, $results);

        $this->assertInstanceOf(ImageGenPartialImageEvent::class, $results[0]->data);
        $this->assertEquals('partial_bytes_flat', $results[0]->data->b64Json);

        $this->assertInstanceOf(ImageGenCompletedEvent::class, $results[1]->data);
        $this->assertEquals('completed_bytes_flat', $results[1]->data->b64Json);
    }
}
