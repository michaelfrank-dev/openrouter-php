<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use MichaelFrank\OpenRouter\Exceptions\ApiResponseException;
use MichaelFrank\OpenRouter\Http\SseStreamParser;
use PHPUnit\Framework\TestCase;

final class SseStreamParserTest extends TestCase
{
    public function testParseStreamChunks(): void
    {
        $chunk1 = [
            'id' => 'chunk-id',
            'choices' => [
                [
                    'index' => 0,
                    'delta' => ['content' => 'Hello']
                ]
            ]
        ];
        $chunk2 = [
            'id' => 'chunk-id',
            'choices' => [
                [
                    'index' => 0,
                    'delta' => ['content' => ' world']
                ]
            ]
        ];

        $streamContent = "data: " . json_encode($chunk1) . "\n\n" .
                         ": comment line\n" .
                         "data: " . json_encode($chunk2) . "\n\n" .
                         "data: [DONE]\n";

        $response = new Response(200, [], Utils::streamFor($streamContent));
        $parser = new SseStreamParser();
        $generator = $parser->parse($response);

        $results = iterator_to_array($generator);

        $this->assertCount(2, $results);

        $delta1 = $results[0]->choices[0]->delta;
        $this->assertNotNull($delta1);
        $this->assertEquals('Hello', $delta1->content);

        $delta2 = $results[1]->choices[0]->delta;
        $this->assertNotNull($delta2);
        $this->assertEquals(' world', $delta2->content);
    }

    public function testStreamInBodyErrorThrowsException(): void
    {
        $errorChunk = [
            'error' => [
                'message' => 'Rate limit exceeded'
            ]
        ];
        $streamContent = "data: " . json_encode($errorChunk) . "\n";
        $response = new Response(200, [], Utils::streamFor($streamContent));
        $parser = new SseStreamParser();

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Rate limit exceeded');

        iterator_to_array($parser->parse($response));
    }

    public function testParseNonSseJsonResponseThrowsException(): void
    {
        $payload = [
            'id' => 'chatcmpl-123',
            'choices' => [
                ['message' => ['content' => 'Hello there']]
            ]
        ];

        $response = new Response(200, ['Content-Type' => 'application/json'], Utils::streamFor(json_encode($payload)));
        $parser = new SseStreamParser();

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('The selected model or provider does not support streaming completions');

        iterator_to_array($parser->parse($response));
    }

    public function testParseNonSseJsonErrorResponseThrowsException(): void
    {
        $payload = [
            'error' => [
                'message' => 'Model not found'
            ]
        ];

        $response = new Response(200, ['Content-Type' => 'application/json'], Utils::streamFor(json_encode($payload)));
        $parser = new SseStreamParser();

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Model not found');

        iterator_to_array($parser->parse($response));
    }
}
