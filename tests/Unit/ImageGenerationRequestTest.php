<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Enums\ImageBackground;
use MichaelFrank\OpenRouter\Enums\ImageOutputFormat;
use MichaelFrank\OpenRouter\Enums\ImageQuality;
use MichaelFrank\OpenRouter\Requests\ImageResolution;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use MichaelFrank\OpenRouter\Requests\ImageGenerationProviderPreferences;
use MichaelFrank\OpenRouter\Requests\ImageGenerationRequest;
use MichaelFrank\OpenRouter\Requests\Messages\ContentPart\ImageContentPart;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageGenerationRequestTest
 *
 * Tests Request VOs: ImageGenerationRequest and ImageGenerationProviderPreferences.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class ImageGenerationRequestTest extends TestCase
{
    public function testValidationEmptyModel(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Model identifier cannot be empty.');
        new ImageGenerationRequest(model: '', prompt: 'panda astronaut');
    }

    public function testValidationEmptyPrompt(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Prompt cannot be empty.');
        new ImageGenerationRequest(model: 'bytedance-seed/seedream-4.5', prompt: '   ');
    }

    public function testValidationNOutOfRangeLow(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Parameter "n" must be between 1 and 10.');
        new ImageGenerationRequest(model: 'bytedance-seed/seedream-4.5', prompt: 'panda', n: 0);
    }

    public function testValidationNOutOfRangeHigh(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Parameter "n" must be between 1 and 10.');
        new ImageGenerationRequest(model: 'bytedance-seed/seedream-4.5', prompt: 'panda', n: 11);
    }

    public function testValidationCompressionOutOfRangeLow(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Parameter "outputCompression" must be between 0 and 100.');
        new ImageGenerationRequest(model: 'bytedance-seed/seedream-4.5', prompt: 'panda', outputCompression: -1);
    }

    public function testValidationCompressionOutOfRangeHigh(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Parameter "outputCompression" must be between 0 and 100.');
        new ImageGenerationRequest(model: 'bytedance-seed/seedream-4.5', prompt: 'panda', outputCompression: 101);
    }

    public function testImmutabilityAndWithBuilders(): void
    {
        $original = new ImageGenerationRequest(model: 'bytedance-seed/seedream-4.5', prompt: 'panda');

        $updated = $original
            ->withModel('another-model')
            ->withPrompt('another prompt')
            ->withAspectRatio('16:9')
            ->withBackground(ImageBackground::Transparent)
            ->withInputReferences([new ImageContentPart('http://example.com/image.jpg')])
            ->withN(5)
            ->withOutputCompression(85)
            ->withOutputFormat(ImageOutputFormat::Webp)
            ->withQuality(ImageQuality::High)
            ->withResolution(new ImageResolution(ImageResolution::TWO_K))
            ->withSeed(42)
            ->withSize('2K')
            ->withStream(true);

        $this->assertNotSame($original, $updated);
        $this->assertEquals('bytedance-seed/seedream-4.5', $original->model);
        $this->assertEquals('panda', $original->prompt);
        $this->assertNull($original->aspectRatio);

        $this->assertEquals('another-model', $updated->model);
        $this->assertEquals('another prompt', $updated->prompt);
        $this->assertEquals('16:9', $updated->aspectRatio);
        $this->assertSame(ImageBackground::Transparent, $updated->background);
        $this->assertCount(1, $updated->inputReferences ?? []);
        $this->assertEquals(5, $updated->n);
        $this->assertEquals(85, $updated->outputCompression);
        $this->assertSame(ImageOutputFormat::Webp, $updated->outputFormat);
        $this->assertSame(ImageQuality::High, $updated->quality);
        $this->assertInstanceOf(ImageResolution::class, $updated->resolution);
        $this->assertEquals(ImageResolution::TWO_K, $updated->resolution->value);
        $this->assertEquals(42, $updated->seed);
        $this->assertEquals('2K', $updated->size);
        $this->assertTrue($updated->stream);
    }

    public function testProviderPreferencesImmutability(): void
    {
        $prefs = new ImageGenerationProviderPreferences();
        $updated = $prefs
            ->withAllowFallbacks(false)
            ->withIgnore(['openai'])
            ->withOnly(['google-ai-studio'])
            ->withOptions(['black-forest-labs' => ['guidance' => 3]])
            ->withOrder(['google-ai-studio']);

        $this->assertNotSame($prefs, $updated);
        $this->assertFalse($updated->allowFallbacks);
        $this->assertEquals(['openai'], $updated->ignore);
        $this->assertEquals(['google-ai-studio'], $updated->only);
        $this->assertEquals(['black-forest-labs' => ['guidance' => 3]], $updated->options);
        $this->assertEquals(['google-ai-studio'], $updated->order);
    }

    public function testSerializationOmitsNullsAndEmptyArrays(): void
    {
        $request = new ImageGenerationRequest(
            model: 'bytedance-seed/seedream-4.5',
            prompt: 'panda',
            n: 1,
            outputFormat: ImageOutputFormat::Png,
            provider: new ImageGenerationProviderPreferences(
                allowFallbacks: true,
                ignore: [] // Empty array -> should be omitted
            )
        );

        $array = $request->toArray();

        $this->assertEquals([
            'model' => 'bytedance-seed/seedream-4.5',
            'prompt' => 'panda',
            'n' => 1,
            'output_format' => 'png',
            'provider' => [
                'allow_fallbacks' => true,
            ]
        ], $array);

        $this->assertEquals(json_encode($array), json_encode($request));
    }

    public function testCustomResolutionStrings(): void
    {
        $request = new ImageGenerationRequest(
            model: 'bytedance-seed/seedream-4.5',
            prompt: 'panda',
            resolution: new ImageResolution('1K')
        );

        $array = $request->toArray();
        $this->assertEquals('1K', $array['resolution']);

        $customRequest = $request->withResolution(new ImageResolution('512x512'));
        $customArray = $customRequest->toArray();
        $this->assertEquals('512x512', $customArray['resolution']);
    }
}
