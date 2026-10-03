<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Responses\ChatMessage;
use MichaelFrank\OpenRouter\Responses\Reasoning\ReasoningDetail;
use MichaelFrank\OpenRouter\Responses\Streaming\ChatMessageChunk;
use PHPUnit\Framework\TestCase;

final class ReasoningDetailsResponseTest extends TestCase
{
    public function testChatMessageParsesReasoningDetails(): void
    {
        $payload = [
            'role' => 'assistant',
            'content' => 'Final answer',
            'reasoning' => 'Plaintext reasoning',
            'reasoning_details' => [
                [
                    'type' => 'reasoning.summary',
                    'summary' => 'Analyzed problem constraints',
                    'id' => 'reasoning-summary-1',
                    'format' => 'anthropic-claude-v1',
                    'index' => 0,
                ],
                [
                    'type' => 'reasoning.encrypted',
                    'data' => 'eyJlbmNyeXB0ZWQiOiJ0cnVlIn0=',
                    'id' => 'reasoning-encrypted-1',
                    'format' => 'anthropic-claude-v1',
                    'index' => 1,
                ],
                [
                    'type' => 'reasoning.text',
                    'text' => 'Let me think systematically',
                    'signature' => 'sha256:abc123def456',
                    'id' => 'reasoning-text-1',
                    'format' => 'anthropic-claude-v1',
                    'index' => 2,
                ],
            ],
        ];

        $message = ChatMessage::fromArray($payload);

        $this->assertSame('Final answer', $message->content);
        $this->assertSame('Plaintext reasoning', $message->reasoning);
        $this->assertCount(3, $message->reasoningDetails);

        $summary = $message->reasoningDetails[0];
        $this->assertSame(ReasoningDetail::TYPE_SUMMARY, $summary->type);
        $this->assertSame('Analyzed problem constraints', $summary->summary);
        $this->assertSame('reasoning-summary-1', $summary->id);
        $this->assertSame('anthropic-claude-v1', $summary->format);
        $this->assertSame(0, $summary->index);

        $encrypted = $message->reasoningDetails[1];
        $this->assertSame(ReasoningDetail::TYPE_ENCRYPTED, $encrypted->type);
        $this->assertSame('eyJlbmNyeXB0ZWQiOiJ0cnVlIn0=', $encrypted->data);

        $text = $message->reasoningDetails[2];
        $this->assertSame(ReasoningDetail::TYPE_TEXT, $text->type);
        $this->assertSame('Let me think systematically', $text->text);
        $this->assertSame('sha256:abc123def456', $text->signature);
    }

    public function testChatMessageChunkParsesReasoningDetails(): void
    {
        $payload = [
            'role' => 'assistant',
            'reasoning_details' => [
                [
                    'type' => 'reasoning.text',
                    'text' => 'Thinking delta...',
                    'id' => 'chunk-reasoning-1',
                ],
            ],
        ];

        $chunk = ChatMessageChunk::fromArray($payload);

        $this->assertCount(1, $chunk->reasoningDetails);
        $this->assertSame('Thinking delta...', $chunk->reasoningDetails[0]->text);
        $this->assertFalse($chunk->isEmpty());
    }

    public function testReasoningDetailToArray(): void
    {
        $detail = new ReasoningDetail(
            type: ReasoningDetail::TYPE_TEXT,
            id: 'r1',
            format: 'anthropic-claude-v1',
            index: 0,
            text: 'Thinking text',
            signature: 'sig123'
        );

        $array = $detail->toArray();

        $this->assertSame(ReasoningDetail::TYPE_TEXT, $array['type']);
        $this->assertSame('r1', $array['id']);
        $this->assertSame('anthropic-claude-v1', $array['format']);
        $this->assertSame(0, $array['index']);
        $this->assertSame('Thinking text', $array['text']);
        $this->assertSame('sig123', $array['signature']);
    }
}
