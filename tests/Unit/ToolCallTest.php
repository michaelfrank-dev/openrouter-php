<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Enums\ToolType;
use MichaelFrank\OpenRouter\Exceptions\ApiResponseException;
use MichaelFrank\OpenRouter\Responses\ToolCall;
use PHPUnit\Framework\TestCase;

/**
 * Class ToolCallTest
 *
 * Unit tests for ToolCall response class and parsing function arguments.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class ToolCallTest extends TestCase
{
    /**
     * Test ToolCall fromArray factory and parseArguments delegation.
     */
    public function testFromArrayAndParseArguments(): void
    {
        $payload = [
            'id' => 'call_123',
            'type' => 'function',
            'function' => [
                'name' => 'get_current_weather',
                'arguments' => '{"location": "Florianopolis", "unit": "celsius"}'
            ]
        ];

        $toolCall = ToolCall::fromArray($payload);

        $this->assertEquals('call_123', $toolCall->id);
        $this->assertEquals(ToolType::Function, $toolCall->type);
        $this->assertEquals('get_current_weather', $toolCall->function->name);

        $args = $toolCall->function->parseArguments();
        $this->assertEquals([
            'location' => 'Florianopolis',
            'unit' => 'celsius'
        ], $args);
    }

    /**
     * Test that parseArguments throws ApiResponseException on invalid JSON.
     */
    public function testParseArgumentsThrowsExceptionOnInvalidJson(): void
    {
        $payload = [
            'id' => 'call_123',
            'type' => 'function',
            'function' => [
                'name' => 'get_current_weather',
                'arguments' => '{"location": "Florianopolis", invalid json here}'
            ]
        ];

        $toolCall = ToolCall::fromArray($payload);

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Failed to parse function call arguments as JSON');
        $toolCall->function->parseArguments();
    }
}
