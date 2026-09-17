<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Integration;

use GuzzleHttp\Psr7\Response;
use Http\Discovery\Psr17FactoryDiscovery;
use MichaelFrank\OpenRouter\Client\OpenRouter;
use MichaelFrank\OpenRouter\Requests\AudioSpeechRequest;
use MichaelFrank\OpenRouter\Requests\AudioTranscriptionRequest;
use MichaelFrank\OpenRouter\Requests\CompletionOptions;
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\EmbeddingRequest;
use MichaelFrank\OpenRouter\Requests\Messages\SystemMessage;
use MichaelFrank\OpenRouter\Requests\Messages\UserMessage;
use MichaelFrank\OpenRouter\Requests\ImageGenerationRequest;
use MichaelFrank\OpenRouter\Requests\ModelListQuery;
use MichaelFrank\OpenRouter\Requests\RerankRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * Class SdkIntegrationTest
 *
 * Verifies that the client coordinates properly with requests, options,
 * HTTP requests, response DTO factories, and custom exceptions.
 *
 * @package MichaelFrank\OpenRouter\Tests\Integration
 */
final class SdkIntegrationTest extends TestCase
{
    private ClientInterface $clientMock;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;
    private UriFactoryInterface $uriFactory;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
    }

    private function getClient(): OpenRouter
    {
        return new OpenRouter(
            $this->clientMock,
            $this->requestFactory,
            $this->streamFactory,
            $this->uriFactory,
            'sk-or-test-key',
            testMode: true
        );
    }

    public function testChatCompletionIntegration(): void
    {
        $payload = [
            'id' => 'gen-112233',
            'model' => 'meta-llama/llama-3-8b-instruct',
            'object' => 'chat.completion',
            'created' => 1719010000,
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'assistant',
                        'content' => 'Llama Response'
                    ],
                    'finish_reason' => 'stop'
                ]
            ],
            'usage' => [
                'prompt_tokens' => 15,
                'completion_tokens' => 25,
                'total_tokens' => 40
            ]
        ];

        $headers = [
            'x-ratelimit-limit' => '100',
            'x-ratelimit-remaining' => '99',
            'x-ratelimit-reset' => '10'
        ];

        $response = new Response(200, $headers, json_encode($payload, JSON_THROW_ON_ERROR));

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();

        $request = new CompletionRequest(
            model: 'meta-llama/llama-3-8b-instruct',
            messages: [
                new SystemMessage('System prompt'),
                new UserMessage('Hello')
            ],
            options: new CompletionOptions(temperature: 0.8)
        );

        $chatResponse = $client->completions($request);

        $this->assertEquals('gen-112233', $chatResponse->id);
        $this->assertEquals('meta-llama/llama-3-8b-instruct', $chatResponse->model);
        $this->assertCount(1, $chatResponse->choices);
        $this->assertEquals('Llama Response', $chatResponse->choices[0]->message->content);
        $this->assertNotNull($chatResponse->usage);
        $this->assertEquals(40, $chatResponse->usage->totalTokens);

        // Check metadata & rate limits parsing
        $rateLimit = $chatResponse->metadata->rateLimit;
        $this->assertEquals(100, $rateLimit->limit);
        $this->assertEquals(99, $rateLimit->remaining);
    }

    public function testEmbeddingsIntegration(): void
    {
        $payload = [
            'model' => 'text-embedding-3-small',
            'object' => 'list',
            'data' => [
                [
                    'object' => 'embedding',
                    'index' => 0,
                    'embedding' => [0.1, 0.2, 0.3]
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR));

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();

        $request = new EmbeddingRequest(
            model: 'text-embedding-3-small',
            input: ['testing text']
        );

        $embedResponse = $client->embeddings($request);

        $this->assertEquals('text-embedding-3-small', $embedResponse->model);
        $this->assertCount(1, $embedResponse->data);
        $this->assertEquals([0.1, 0.2, 0.3], $embedResponse->data[0]->embedding);
    }

    public function testRerankIntegration(): void
    {
        $payload = [
            'id' => 'rerank-123',
            'model' => 'cohere/rerank-english-v3.0',
            'results' => [
                [
                    'index' => 1,
                    'relevance_score' => 0.95
                ]
            ],
            'usage' => [
                'total_tokens' => 12
            ]
        ];

        $response = new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR));

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();

        $request = new RerankRequest(
            model: 'cohere/rerank-english-v3.0',
            query: 'Which is better?',
            documents: ['Doc A', 'Doc B']
        );

        $rerankResponse = $client->rerank($request);

        $this->assertEquals('rerank-123', $rerankResponse->id);
        $this->assertEquals('cohere/rerank-english-v3.0', $rerankResponse->model);
        $this->assertCount(1, $rerankResponse->results);
        $this->assertEquals(0.95, $rerankResponse->results[0]->relevanceScore);
        $this->assertNotNull($rerankResponse->usage);
        $this->assertEquals(12, $rerankResponse->usage->totalTokens);
    }

    public function testAudioSpeechIntegration(): void
    {
        $speechContent = 'synthesized audio content';
        $response = new Response(200, ['content-type' => 'audio/mpeg'], $speechContent);

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();

        $request = new AudioSpeechRequest(
            model: 'openai/tts-1',
            input: 'Test speech output',
            voice: 'alloy'
        );

        $speechResponse = $client->speech($request);

        $this->assertEquals($speechContent, $speechResponse->getContents());
    }

    public function testAudioTranscriptionIntegration(): void
    {
        $payload = [
            'text' => 'Transcribed speech text',
            'duration' => 1.5,
            'task' => 'transcribe',
            'language' => 'english'
        ];

        $response = new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR));

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();

        $tempFile = tempnam(sys_get_temp_dir(), 'test_audio');
        $this->assertNotFalse($tempFile);
        file_put_contents($tempFile, 'dummy audio data');

        try {
            $request = AudioTranscriptionRequest::fromFile(
                path: $tempFile,
                model: 'openai/whisper-1'
            );

            $transcribeResponse = $client->transcriptions($request);

            $this->assertEquals('Transcribed speech text', $transcribeResponse->text);
        } finally {
            unlink($tempFile);
        }
    }

    public function testModelsListIntegration(): void
    {
        $payload = [
            'data' => [
                [
                    'id' => 'openai/gpt-4o',
                    'name' => 'GPT-4o',
                    'description' => 'Latest GPT-4 model',
                    'pricing' => [
                        'prompt' => '0.000005',
                        'completion' => '0.000015'
                    ]
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR));

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();

        $query = new ModelListQuery();
        $modelsResponse = $client->models($query);

        $this->assertCount(1, $modelsResponse->data);
        $this->assertEquals('openai/gpt-4o', $modelsResponse->data[0]->id);
        $this->assertEquals('GPT-4o', $modelsResponse->data[0]->name);
        $this->assertNotNull($modelsResponse->data[0]->pricing);
        $this->assertEquals(0.000005, $modelsResponse->data[0]->pricing->prompt);
    }

    public function testCreditsIntegration(): void
    {
        $payload = [
            'data' => [
                'total_credits' => 100.5,
                'total_usage' => 25.75
            ]
        ];

        $response = new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR));

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();

        $creditsResponse = $client->credits();

        $this->assertEquals(100.5, $creditsResponse->creditsPurchased);
        $this->assertEquals(25.75, $creditsResponse->creditsUsed);
        $this->assertEquals(74.75, $creditsResponse->creditsRemaining);
    }

    public function testImageGenerationIntegration(): void
    {
        $payload = [
            'created' => 1748372400,
            'data' => [
                [
                    'b64_json' => 'encoded_bytes'
                ]
            ],
            'usage' => [
                'prompt_tokens' => 0,
                'completion_tokens' => 10,
                'total_tokens' => 10,
                'cost' => 0.01
            ]
        ];

        $response = new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR));

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();

        $request = new ImageGenerationRequest(
            model: 'bytedance-seed/seedream-4.5',
            prompt: 'panda astronaut'
        );

        $imgResponse = $client->images($request);

        $this->assertEquals(1748372400, $imgResponse->created);
        $this->assertCount(1, $imgResponse->data);
        $this->assertEquals('encoded_bytes', $imgResponse->data[0]->b64Json);
        $this->assertNotNull($imgResponse->usage);
        $this->assertEquals(10, $imgResponse->usage->totalTokens);
    }

    public function testImageGenerationStreamingIntegration(): void
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
                         "data: " . json_encode($event2) . "\n\n" .
                         "data: [DONE]\n";

        $response = new Response(200, [], \GuzzleHttp\Psr7\Utils::streamFor($streamContent));

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();

        $request = new ImageGenerationRequest(
            model: 'bytedance-seed/seedream-4.5',
            prompt: 'panda astronaut'
        );

        $generator = $client->streamImages($request);
        $results = iterator_to_array($generator);

        $this->assertCount(2, $results);
        $this->assertInstanceOf(\MichaelFrank\OpenRouter\Responses\Streaming\ImageGenPartialImageEvent::class, $results[0]->data);
        $this->assertEquals('partial_bytes', $results[0]->data->b64Json);
        $this->assertInstanceOf(\MichaelFrank\OpenRouter\Responses\Streaming\ImageGenCompletedEvent::class, $results[1]->data);
        $this->assertEquals('completed_bytes', $results[1]->data->b64Json);
    }

    public function testImageModelsListIntegration(): void
    {
        $payload = [
            'data' => [
                [
                    'id' => 'bytedance-seed/seedream-4.5',
                    'name' => 'Seedream 4.5',
                    'description' => 'A text-to-image model.',
                    'created' => 1692901234,
                    'architecture' => [
                        'input_modalities' => ['text'],
                        'output_modalities' => ['image']
                    ],
                    'supported_parameters' => [
                        'resolution' => [
                            'type' => 'enum',
                            'values' => ['1K', '2K']
                        ]
                    ],
                    'supports_streaming' => false,
                    'endpoints' => '/api/v1/images/models/bytedance-seed/seedream-4.5/endpoints'
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR));

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();
        $listResponse = $client->imageModels();

        $this->assertCount(1, $listResponse->data);
        $this->assertEquals('bytedance-seed/seedream-4.5', $listResponse->data[0]->id);
    }

    public function testImageModelEndpointsIntegration(): void
    {
        $payload = [
            'id' => 'bytedance-seed/seedream-4.5',
            'endpoints' => [
                [
                    'allowed_passthrough_parameters' => [],
                    'pricing' => [
                        [
                            'billable' => 'output_image',
                            'cost_usd' => 0.05,
                            'unit' => 'image'
                        ]
                    ],
                    'provider_name' => 'Bytedance',
                    'provider_slug' => 'bytedance',
                    'provider_tag' => null,
                    'supported_parameters' => [],
                    'supports_streaming' => false
                ]
            ]
        ];

        $response = new Response(200, [], json_encode($payload, JSON_THROW_ON_ERROR));

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = $this->getClient();
        $endpointsResponse = $client->imageModelEndpoints('bytedance-seed', 'seedream-4.5');

        $this->assertEquals('bytedance-seed/seedream-4.5', $endpointsResponse->id);
        $this->assertCount(1, $endpointsResponse->endpoints);
        $this->assertEquals('Bytedance', $endpointsResponse->endpoints[0]->providerName);
    }

    public function testImageModelEndpointsValidationThrowsOnEmptyAuthor(): void
    {
        $this->expectException(\MichaelFrank\OpenRouter\Exceptions\ValidationException::class);
        $this->expectExceptionMessage('Model author cannot be empty.');

        $client = $this->getClient();
        $client->imageModelEndpoints('', 'slug');
    }

    public function testImageModelEndpointsValidationThrowsOnEmptySlug(): void
    {
        $this->expectException(\MichaelFrank\OpenRouter\Exceptions\ValidationException::class);
        $this->expectExceptionMessage('Model slug cannot be empty.');

        $client = $this->getClient();
        $client->imageModelEndpoints('author', '');
    }
}
