<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MichaelFrank\OpenRouter\Responses\GenerationData;

/**
 * Class GenerationDataTest
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class GenerationDataTest extends TestCase
{
    /**
     * Test instantiation and parsing of all fields from a payload array.
     */
    public function testFromArrayParsesAllPropertiesCorrectly(): void
    {
        $payload = [
            'id' => 'gen-12345',
            'model' => 'openai/gpt-4',
            'provider' => 'OpenAI',
            'price' => 0.00015,
            'cache_discount' => 0.00005,
            'tokens' => 150,
            'tokens_prompt' => 100,
            'tokens_completion' => 50,
            'native_tokens_prompt' => 98,
            'native_tokens_completion' => 48,
            'native_tokens_reasoning' => 10,
            'native_tokens_cached' => 40,
            'latency' => 1250.5,
            'finish_reason' => 'stop',
            'created_at' => '2026-08-01T12:00:00Z',
        ];

        $data = GenerationData::fromArray($payload);

        $this->assertSame('gen-12345', $data->id);
        $this->assertSame('openai/gpt-4', $data->model);
        $this->assertSame('OpenAI', $data->provider);
        $this->assertSame(0.00015, $data->price);
        $this->assertSame(0.00005, $data->cacheDiscount);

        // Standard token counts (should be ints)
        $this->assertSame(150, $data->tokens);
        $this->assertSame(100, $data->tokensPrompt);
        $this->assertSame(50, $data->tokensCompletion);

        // Native token counts (should be ints)
        $this->assertSame(98, $data->nativeTokensPrompt);
        $this->assertSame(48, $data->nativeTokensCompletion);
        $this->assertSame(10, $data->nativeTokensReasoning);
        $this->assertSame(40, $data->nativeTokensCached);

        $this->assertSame(1250.5, $data->latency);
        $this->assertSame('stop', $data->finishReason);
        $this->assertInstanceOf(\DateTimeImmutable::class, $data->createdAt);
        $this->assertSame('2026-08-01T12:00:00+00:00', $data->createdAt->format(\DateTimeInterface::ATOM));
    }

    /**
     * Test mapping of fallback properties (e.g., total_cost, provider_name, total_tokens, cache_discount)
     */
    public function testFromArrayWithFallbackKeys(): void
    {
        $payload = [
            'provider_name' => 'DeepSeek',
            'total_cost' => 0.00045,
            'cache_discount' => 0.0001,
            'total_tokens' => 200,
        ];

        $data = GenerationData::fromArray($payload);

        $this->assertSame('DeepSeek', $data->provider);
        $this->assertSame(0.00045, $data->price);
        $this->assertSame(0.0001, $data->cacheDiscount);
        $this->assertSame(200, $data->tokens);
    }

    /**
     * Test that numeric string representations are cast to their correct types.
     */
    public function testCastsNumericStringsToCorrectTypes(): void
    {
        $payload = [
            'price' => '0.003',
            'cache_discount' => '0.0001',
            'tokens' => '120',
            'tokens_prompt' => '80',
            'tokens_completion' => '40',
            'native_tokens_prompt' => '78',
            'native_tokens_completion' => '38',
            'native_tokens_reasoning' => '5',
            'native_tokens_cached' => '20',
            'latency' => '950',
        ];

        $data = GenerationData::fromArray($payload);

        $this->assertSame(0.003, $data->price);
        $this->assertSame(0.0001, $data->cacheDiscount);
        $this->assertSame(120, $data->tokens);
        $this->assertSame(80, $data->tokensPrompt);
        $this->assertSame(40, $data->tokensCompletion);
        $this->assertSame(78, $data->nativeTokensPrompt);
        $this->assertSame(38, $data->nativeTokensCompletion);
        $this->assertSame(5, $data->nativeTokensReasoning);
        $this->assertSame(20, $data->nativeTokensCached);
        $this->assertSame(950.0, $data->latency);
    }

    /**
     * Test handling of missing or null values in payload.
     */
    public function testHandlesNullValuesGracefully(): void
    {
        $data = GenerationData::fromArray([]);

        $this->assertNull($data->id);
        $this->assertNull($data->model);
        $this->assertNull($data->provider);
        $this->assertNull($data->price);
        $this->assertNull($data->cacheDiscount);
        $this->assertNull($data->tokens);
        $this->assertNull($data->tokensPrompt);
        $this->assertNull($data->tokensCompletion);
        $this->assertNull($data->nativeTokensPrompt);
        $this->assertNull($data->nativeTokensCompletion);
        $this->assertNull($data->nativeTokensReasoning);
        $this->assertNull($data->nativeTokensCached);
        $this->assertNull($data->latency);
        $this->assertNull($data->finishReason);
        $this->assertNull($data->createdAt);
    }
}
