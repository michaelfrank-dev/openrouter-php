<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Requests\CompletionOptions;
use MichaelFrank\OpenRouter\Requests\Reasoning\ReasoningConfig;
use MichaelFrank\OpenRouter\Requests\Reasoning\ReasoningEffort;
use PHPUnit\Framework\TestCase;

final class CompletionOptionsReasoningTest extends TestCase
{
    public function testWithReasoning(): void
    {
        $options = new CompletionOptions();
        $reasoning = ReasoningConfig::withEffort(ReasoningEffort::HIGH);

        $newOptions = $options->withReasoning($reasoning);

        $this->assertNotSame($options, $newOptions);
        $this->assertNull($options->reasoning);
        $this->assertSame($reasoning, $newOptions->reasoning);

        $array = $newOptions->toArray();
        $this->assertArrayHasKey('reasoning', $array);
        $reasoningArray = $array['reasoning'] ?? null;
        $this->assertIsArray($reasoningArray);
        $this->assertSame(ReasoningEffort::HIGH, $reasoningArray['effort']);
    }

    public function testWithReasoningEffort(): void
    {
        $options = new CompletionOptions();
        $newOptions = $options->withReasoningEffort('medium');

        $this->assertNotSame($options, $newOptions);
        $this->assertNotNull($newOptions->reasoning);
        $this->assertSame('medium', $newOptions->reasoning->effort?->value);

        $array = $newOptions->toArray();
        $this->assertSame(['effort' => 'medium'], $array['reasoning']);
    }

    public function testWithReasoningTokens(): void
    {
        $options = new CompletionOptions();
        $newOptions = $options->withReasoningTokens(1500);

        $this->assertNotSame($options, $newOptions);
        $this->assertNotNull($newOptions->reasoning);
        $this->assertSame(1500, $newOptions->reasoning->maxTokens);

        $array = $newOptions->toArray();
        $this->assertSame(['max_tokens' => 1500], $array['reasoning']);
    }

    public function testWithoutReasoningResetsToNull(): void
    {
        $options = (new CompletionOptions())
            ->withReasoningEffort(ReasoningEffort::HIGH);

        $cleared = $options->withoutReasoning();

        $this->assertNotSame($options, $cleared);
        $this->assertNull($cleared->reasoning);
        $this->assertArrayNotHasKey('reasoning', $cleared->toArray());
    }

    public function testLegacyIncludeReasoningFallback(): void
    {
        $legacy = new CompletionOptions(includeReasoning: true);
        $array = $legacy->toArray();

        $this->assertTrue($array['include_reasoning']);
        $this->assertArrayNotHasKey('reasoning', $array);

        $withReasoning = $legacy->withReasoningEffort(ReasoningEffort::LOW);
        $overriddenArray = $withReasoning->toArray();

        $this->assertArrayHasKey('reasoning', $overriddenArray);
        $this->assertArrayNotHasKey('include_reasoning', $overriddenArray);
    }

    public function testExtraParametersMerging(): void
    {
        $options = (new CompletionOptions(temperature: 0.7))
            ->withExtraParameter('provider_bypass', true)
            ->withExtraParameters(['beta_feature' => 'enabled', 'custom_weight' => 42]);

        $this->assertTrue($options->extraParameters['provider_bypass']);
        $this->assertSame('enabled', $options->extraParameters['beta_feature']);
        $this->assertSame(42, $options->extraParameters['custom_weight']);

        $array = $options->toArray();
        $this->assertSame(0.7, $array['temperature']);
        $this->assertTrue($array['provider_bypass']);
        $this->assertSame('enabled', $array['beta_feature']);
        $this->assertSame(42, $array['custom_weight']);
    }

    public function testTypedFieldsPrecedenceOverExtraParameters(): void
    {
        $options = (new CompletionOptions(temperature: 0.9))
            ->withExtraParameter('temperature', 0.2);

        $array = $options->toArray();
        $this->assertSame(0.9, $array['temperature']);
    }

    public function testFromArrayIngestsReasoningAndExtraParameters(): void
    {
        $data = [
            'temperature' => 0.5,
            'reasoning' => [
                'effort' => 'high',
                'exclude' => false,
            ],
            'custom_unmapped_field' => 'foo_bar',
        ];

        $options = CompletionOptions::fromArray($data);

        $this->assertSame(0.5, $options->temperature);
        $this->assertNotNull($options->reasoning);
        $this->assertSame('high', $options->reasoning->effort?->value);
        $this->assertFalse($options->reasoning->exclude);
        $this->assertSame('foo_bar', $options->extraParameters['custom_unmapped_field'] ?? null);
    }
}
