<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MichaelFrank\OpenRouter\Requests\AudioTranscriptionRequest;
use MichaelFrank\OpenRouter\Requests\ProviderPreferences;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;

/**
 * Class AudioTranscriptionRequestTest
 *
 * Tests the initialization, factory, and serialization behavior of AudioTranscriptionRequest.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class AudioTranscriptionRequestTest extends TestCase
{
    /**
     * Tests that toArray structures the payload correctly for OpenRouter.
     *
     * @return void
     */
    public function testToArrayStructuresPayloadCorrectly(): void
    {
        $provider = new ProviderPreferences(allowFallbacks: true);
        $request = new AudioTranscriptionRequest(
            model: 'openai/whisper-large-v3',
            audioData: 'YmFzZTY0IGRhdGE=',
            format: 'mp3',
            language: 'en',
            temperature: 0.5,
            provider: $provider
        );

        $array = $request->toArray();

        $expected = [
            'model' => 'openai/whisper-large-v3',
            'input_audio' => [
                'data' => 'YmFzZTY0IGRhdGE=',
                'format' => 'mp3',
            ],
            'language' => 'en',
            'temperature' => 0.5,
            'provider' => $provider->toArray(),
        ];

        $this->assertEquals($expected, $array);
        $this->assertEquals($expected, $request->jsonSerialize());
    }

    /**
     * Tests that optional null fields are omitted in toArray.
     *
     * @return void
     */
    public function testToArrayOmitsOptionalNullFields(): void
    {
        $request = new AudioTranscriptionRequest(
            model: 'openai/whisper-large-v3',
            audioData: 'YmFzZTY0IGRhdGE=',
            format: 'mp3'
        );

        $array = $request->toArray();

        $expected = [
            'model' => 'openai/whisper-large-v3',
            'input_audio' => [
                'data' => 'YmFzZTY0IGRhdGE=',
                'format' => 'mp3',
            ],
        ];

        $this->assertEquals($expected, $array);
    }

    /**
     * Tests that fromFile throws InvalidArgumentException when file does not exist.
     *
     * @return void
     */
    public function testFromFileThrowsExceptionWhenFileDoesNotExist(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Audio file not found: non_existent_file.mp3');

        AudioTranscriptionRequest::fromFile('non_existent_file.mp3', 'openai/whisper-large-v3');
    }

    /**
     * Tests that fromFile constructs request correctly when file exists.
     *
     * @return void
     */
    public function testFromFileConstructsRequestCorrectly(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_audio');
        $this->assertNotFalse($tempFile);
        $content = 'dummy audio content';
        file_put_contents($tempFile, $content);

        try {
            $request = AudioTranscriptionRequest::fromFile($tempFile, 'openai/whisper-large-v3', 'es');

            $this->assertEquals('openai/whisper-large-v3', $request->model);
            $this->assertEquals(base64_encode($content), $request->audioData);
            $this->assertEquals('es', $request->language);

            $array = $request->toArray();
            $this->assertEquals([
                'data' => base64_encode($content),
                'format' => strtolower(pathinfo($tempFile, PATHINFO_EXTENSION)),
            ], $array['input_audio']);
        } finally {
            unlink($tempFile);
        }
    }
}
