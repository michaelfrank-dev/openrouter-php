<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MichaelFrank\OpenRouter\Requests\AudioSpeechRequest;
use MichaelFrank\OpenRouter\Requests\ProviderPreferences;

/**
 * Class AudioSpeechRequestTest
 *
 * Tests the serialization and properties of AudioSpeechRequest.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class AudioSpeechRequestTest extends TestCase
{
    /**
     * Tests that toArray structures the payload correctly for OpenRouter.
     *
     * @return void
     */
    public function testToArrayStructuresPayloadCorrectly(): void
    {
        $provider = new ProviderPreferences(allowFallbacks: true);
        $request = new AudioSpeechRequest(
            model: 'hexgrad/kokoro-82m',
            input: 'Test speech input',
            voice: 'af_alloy',
            responseFormat: 'mp3',
            speed: 1.0,
            provider: $provider
        );

        $array = $request->toArray();

        $expected = [
            'model' => 'hexgrad/kokoro-82m',
            'input' => 'Test speech input',
            'voice' => 'af_alloy',
            'response_format' => 'mp3',
            'speed' => 1.0,
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
        $request = new AudioSpeechRequest(
            model: 'hexgrad/kokoro-82m',
            input: 'Test speech input',
            voice: 'af_alloy'
        );

        $array = $request->toArray();

        $expected = [
            'model' => 'hexgrad/kokoro-82m',
            'input' => 'Test speech input',
            'voice' => 'af_alloy',
            'response_format' => 'pcm',
        ];

        $this->assertEquals($expected, $array);
    }
}
