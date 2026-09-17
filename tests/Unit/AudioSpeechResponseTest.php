<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use GuzzleHttp\Psr7\Utils;
use MichaelFrank\OpenRouter\Metadata\RateLimit;
use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Responses\AudioSpeechResponse;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

final class AudioSpeechResponseTest extends TestCase
{
    public function testLazyLoadAndSave(): void
    {
        $content = 'binary_speech_payload';
        $stream = Utils::streamFor($content);
        $metadata = new ResponseMetadata('req-1', new RateLimit(null, null, null));

        $audioResponse = new AudioSpeechResponse($stream, $metadata);

        $this->assertEquals($content, $audioResponse->getContents());

        $tempDir = __DIR__ . '/../../scratch/speech_test/nested';
        $tempPath = $tempDir . '/speech.pcm';

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
        if (is_dir($tempDir)) {
            rmdir($tempDir);
        }
        if (is_dir(dirname($tempDir))) {
            rmdir(dirname($tempDir));
        }

        $audioResponse->saveToFile($tempPath);

        $this->assertFileExists($tempPath);
        $this->assertEquals($content, file_get_contents($tempPath));

        // Clean up
        unlink($tempPath);
        rmdir($tempDir);
        rmdir(dirname($tempDir));
    }

    public function testSaveToFileThrowsExceptionOnFailure(): void
    {
        $stream = Utils::streamFor('speech-data');
        $metadata = new ResponseMetadata('req-1', new RateLimit(null, null, null));
        $audioResponse = new AudioSpeechResponse($stream, $metadata);

        $this->expectException(ValidationException::class);
        $audioResponse->saveToFile('');
    }
}
