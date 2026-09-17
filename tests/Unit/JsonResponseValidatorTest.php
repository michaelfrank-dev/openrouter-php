<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use MichaelFrank\OpenRouter\Exceptions\ApiResponseException;
use MichaelFrank\OpenRouter\Http\JsonResponseValidator;
use PHPUnit\Framework\TestCase;

final class JsonResponseValidatorTest extends TestCase
{
    public function testDecodeValidJson(): void
    {
        $bodyString = json_encode(['foo' => 'bar']);
        $this->assertIsString($bodyString);

        $response = new Response(200, [], $bodyString);
        $validator = new JsonResponseValidator();
        $data = $validator->decode($response);

        $this->assertEquals(['foo' => 'bar'], $data);
    }

    public function testDecodeMalformedJsonThrowsException(): void
    {
        $response = new Response(200, [], '{invalid_json}');
        $validator = new JsonResponseValidator();

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Failed to decode JSON response');
        $validator->decode($response);
    }

    public function testDecodeInBodyErrorThrowsException(): void
    {
        $errorBody = json_encode([
            'error' => [
                'message' => 'Insufficient credits',
                'code' => 402
            ]
        ]);
        $this->assertIsString($errorBody);

        $response = new Response(200, [], $errorBody);
        $validator = new JsonResponseValidator();

        $this->expectException(ApiResponseException::class);
        $this->expectExceptionMessage('Insufficient credits');
        $validator->decode($response);
    }
}
