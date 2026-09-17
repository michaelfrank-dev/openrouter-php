<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Responses\ServerToolUse;
use MichaelFrank\OpenRouter\Responses\TokenDetails\PromptTokensDetails;
use MichaelFrank\OpenRouter\Responses\Usage;
use PHPUnit\Framework\TestCase;

/**
 * Class UsageTest
 *
 * Tests the Usage response DTOs and their nested details classes.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class UsageTest extends TestCase
{
    public function testServerToolUseFromArray(): void
    {
        $payload = [
            'web_search_requests' => 3,
            'tool_calls_executed' => 4,
            'tool_calls_requested' => 5,
        ];

        $toolUse = ServerToolUse::fromArray($payload);

        $this->assertEquals(3, $toolUse->webSearchRequests);
        $this->assertEquals(4, $toolUse->toolCallsExecuted);
        $this->assertEquals(5, $toolUse->toolCallsRequested);
    }

    public function testServerToolUseHandlesNullGracefully(): void
    {
        $toolUse = ServerToolUse::fromArray([]);

        $this->assertNull($toolUse->webSearchRequests);
        $this->assertNull($toolUse->toolCallsExecuted);
        $this->assertNull($toolUse->toolCallsRequested);
    }

    public function testPromptTokensDetailsFromArray(): void
    {
        $payload = [
            'cached_tokens' => 100,
            'cache_write_tokens' => 50,
            'audio_tokens' => 25,
            'video_tokens' => 10,
            'file_tokens' => 5,
        ];

        $details = PromptTokensDetails::fromArray($payload);

        $this->assertEquals(100, $details->cachedTokens);
        $this->assertEquals(50, $details->cacheWriteTokens);
        $this->assertEquals(25, $details->audioTokens);
        $this->assertEquals(10, $details->videoTokens);
        $this->assertEquals(5, $details->fileTokens);
    }

    public function testPromptTokensDetailsHandlesNullGracefully(): void
    {
        $details = PromptTokensDetails::fromArray([]);

        $this->assertNull($details->cachedTokens);
        $this->assertNull($details->cacheWriteTokens);
        $this->assertNull($details->audioTokens);
        $this->assertNull($details->videoTokens);
        $this->assertNull($details->fileTokens);
    }

    public function testUsageFromArray(): void
    {
        $payload = [
            'prompt_tokens' => 10,
            'completion_tokens' => 20,
            'total_tokens' => 30,
            'cost' => 0.0015,
            'service_tier' => 'fast',
            'is_byok' => true,
        ];

        $usage = Usage::fromArray($payload);

        $this->assertEquals(10, $usage->promptTokens);
        $this->assertEquals(20, $usage->completionTokens);
        $this->assertEquals(30, $usage->totalTokens);
        $this->assertEquals(0.0015, $usage->cost);
        $this->assertEquals('fast', $usage->serviceTier);
        $this->assertTrue($usage->isByok);
    }

    public function testUsageHandlesNullGracefully(): void
    {
        $usage = Usage::fromArray([]);

        $this->assertEquals(0, $usage->promptTokens);
        $this->assertEquals(0, $usage->completionTokens);
        $this->assertEquals(0, $usage->totalTokens);
        $this->assertNull($usage->cost);
        $this->assertNull($usage->serviceTier);
        $this->assertNull($usage->isByok);
        $this->assertNull($usage->promptTokensDetails);
        $this->assertNull($usage->completionTokensDetails);
        $this->assertNull($usage->costDetails);
        $this->assertNull($usage->serverToolUse);
    }
}
