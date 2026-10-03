<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use MichaelFrank\OpenRouter\Requests\Reasoning\ReasoningConfig;
use MichaelFrank\OpenRouter\Requests\Reasoning\ReasoningContext;
use MichaelFrank\OpenRouter\Requests\Reasoning\ReasoningEffort;
use MichaelFrank\OpenRouter\Requests\Reasoning\ReasoningMode;
use PHPUnit\Framework\TestCase;

final class ReasoningConfigTest extends TestCase
{
    public function testSerializationWithEffort(): void
    {
        $config = new ReasoningConfig(
            effort: new ReasoningEffort(ReasoningEffort::HIGH),
            exclude: false,
            context: new ReasoningContext(ReasoningContext::ALL_TURNS),
            mode: new ReasoningMode(ReasoningMode::PRO)
        );

        $array = $config->toArray();

        $this->assertSame(ReasoningEffort::HIGH, $array['effort']);
        $this->assertFalse($array['exclude']);
        $this->assertSame(ReasoningContext::ALL_TURNS, $array['context']);
        $this->assertSame(ReasoningMode::PRO, $array['mode']);
        $this->assertArrayNotHasKey('max_tokens', $array);
        $this->assertArrayNotHasKey('enabled', $array);

        $this->assertSame($array, $config->jsonSerialize());
    }

    public function testSerializationWithMaxTokens(): void
    {
        $config = new ReasoningConfig(
            maxTokens: 2048,
            exclude: true
        );

        $array = $config->toArray();

        $this->assertSame(2048, $array['max_tokens']);
        $this->assertTrue($array['exclude']);
        $this->assertArrayNotHasKey('effort', $array);
    }

    public function testAcceptsStringForWrapperFields(): void
    {
        $config = new ReasoningConfig(
            effort: 'xhigh',
            context: 'current_turn',
            mode: 'standard'
        );

        $array = $config->toArray();

        $this->assertSame('xhigh', $array['effort']);
        $this->assertSame('current_turn', $array['context']);
        $this->assertSame('standard', $array['mode']);
    }

    public function testThrowsExceptionWhenBothEffortAndMaxTokensAreProvided(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Cannot specify both "effort" and "maxTokens" for reasoning.');

        new ReasoningConfig(
            effort: ReasoningEffort::HIGH,
            maxTokens: 1000
        );
    }

    public function testThrowsExceptionOnNegativeMaxTokens(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Reasoning maxTokens must be non-negative.');

        new ReasoningConfig(maxTokens: -10);
    }

    public function testStaticBuilders(): void
    {
        $effortConfig = ReasoningConfig::withEffort(ReasoningEffort::MEDIUM, exclude: true);
        $this->assertSame(ReasoningEffort::MEDIUM, $effortConfig->effort?->value);
        $this->assertTrue($effortConfig->exclude);

        $tokensConfig = ReasoningConfig::withMaxTokens(4096);
        $this->assertSame(4096, $tokensConfig->maxTokens);

        $disabledConfig = ReasoningConfig::disabled();
        $this->assertFalse($disabledConfig->enabled);

        $defaultConfig = ReasoningConfig::default();
        $this->assertTrue($defaultConfig->enabled);
    }

    public function testFromArray(): void
    {
        $data = [
            'effort' => 'low',
            'exclude' => false,
            'enabled' => true,
            'context' => 'auto',
            'mode' => 'standard',
        ];

        $config = ReasoningConfig::fromArray($data);

        $this->assertSame('low', $config->effort?->value);
        $this->assertFalse($config->exclude);
        $this->assertTrue($config->enabled);
        $this->assertSame('auto', $config->context?->value);
        $this->assertSame('standard', $config->mode?->value);
    }
}
