<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Enums\ImageBackground;
use MichaelFrank\OpenRouter\Enums\ImageOutputFormat;
use MichaelFrank\OpenRouter\Enums\ImageQuality;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use MichaelFrank\OpenRouter\Requests\Tools\OpenRouterImageGenerationTool;
use PHPUnit\Framework\TestCase;

/**
 * Class OpenRouterImageGenerationToolTest
 *
 * Tests serialization and validation for OpenRouterImageGenerationTool.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class OpenRouterImageGenerationToolTest extends TestCase
{
    public function testSerializationWithParams(): void
    {
        $tool = new OpenRouterImageGenerationTool(
            model: 'openai/gpt-image-2',
            quality: ImageQuality::High,
            aspectRatio: '16:9',
            size: '1024x1024',
            background: ImageBackground::Transparent,
            outputFormat: ImageOutputFormat::Png,
            outputCompression: 85,
            moderation: 'auto'
        );

        $array = $tool->toArray();

        $this->assertEquals('openrouter:image_generation', $array['type']);
        $this->assertArrayHasKey('parameters', $array);

        $params = $array['parameters'] ?? [];
        $this->assertEquals('openai/gpt-image-2', $params['model'] ?? null);
        $this->assertEquals('high', $params['quality'] ?? null);
        $this->assertEquals('16:9', $params['aspect_ratio']);
        $this->assertEquals('1024x1024', $params['size']);
        $this->assertEquals('transparent', $params['background']);
        $this->assertEquals('png', $params['output_format']);
        $this->assertEquals(85, $params['output_compression']);
        $this->assertEquals('auto', $params['moderation']);
        $this->assertEquals($array, $tool->jsonSerialize());
    }

    public function testSerializationEmpty(): void
    {
        $tool = new OpenRouterImageGenerationTool();
        $array = $tool->toArray();

        $this->assertEquals('openrouter:image_generation', $array['type']);
        $this->assertArrayNotHasKey('parameters', $array);
    }

    public function testInvalidCompressionThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Parameter "outputCompression" must be between 0 and 100.');

        new OpenRouterImageGenerationTool(outputCompression: 101);
    }

    public function testInvalidNegativeCompressionThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Parameter "outputCompression" must be between 0 and 100.');

        new OpenRouterImageGenerationTool(outputCompression: -1);
    }
}
