<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Requests\Messages\AssistantMessage;
use MichaelFrank\OpenRouter\Requests\Messages\SystemMessage;
use PHPUnit\Framework\TestCase;

final class AssistantMessageReasoningTest extends TestCase
{
    public function testAssistantMessagePreservesReasoningAndDetails(): void
    {
        $message = new AssistantMessage(
            content: 'Hello world',
            reasoning: 'Detailed reasoning string',
            reasoningDetails: [
                [
                    'type' => 'reasoning.summary',
                    'summary' => 'Preserved summary block',
                ],
            ]
        );

        $array = $message->toArray();

        $this->assertSame('assistant', $array['role']);
        $this->assertSame('Hello world', $array['content']);
        $this->assertSame('Detailed reasoning string', $array['reasoning']);
        $this->assertSame([
            [
                'type' => 'reasoning.summary',
                'summary' => 'Preserved summary block',
            ],
        ], $array['reasoning_details']);
    }

    public function testSystemMessageSupportsConfigurationUpdate(): void
    {
        $message = new SystemMessage(
            content: '',
            configurationUpdate: [
                'reasoning' => [
                    'effort' => 'low',
                ],
            ]
        );

        $array = $message->toArray();

        $this->assertSame('system', $array['role']);
        $this->assertSame('', $array['content']);
        $this->assertSame(['reasoning' => ['effort' => 'low']], $array['configuration_update']);
    }
}
