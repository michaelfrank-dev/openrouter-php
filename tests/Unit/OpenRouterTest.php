<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use Http\Discovery\Psr17FactoryDiscovery;
use MichaelFrank\OpenRouter\Client\OpenRouter;
use MichaelFrank\OpenRouter\Exceptions\ApiRequestException;
use MichaelFrank\OpenRouter\Exceptions\ConfigurationException;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

final class OpenRouterTest extends TestCase
{
    private ClientInterface $clientMock;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;
    private UriFactoryInterface $uriFactory;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();
        $this->uriFactory = Psr17FactoryDiscovery::findUriFactory();
    }

    public function testEmptyApiKeyThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('API key cannot be empty');

        new OpenRouter(
            $this->clientMock,
            $this->requestFactory,
            $this->streamFactory,
            $this->uriFactory,
            ''
        );
    }

    public function testNonHttpsBaseUriThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('must use HTTPS');

        new OpenRouter(
            $this->clientMock,
            $this->requestFactory,
            $this->streamFactory,
            $this->uriFactory,
            'sk-key',
            'http://unsecured-url.ai'
        );
    }

    public function testNonHttpsBaseUriAllowedInTestMode(): void
    {
        $client = new OpenRouter(
            $this->clientMock,
            $this->requestFactory,
            $this->streamFactory,
            $this->uriFactory,
            'sk-key',
            'http://unsecured-url.ai',
            testMode: true
        );

        $this->assertInstanceOf(OpenRouter::class, $client);
    }

    public function testHttpRequestFailureThrowsApiRequestException(): void
    {
        $body = json_encode(['error' => 'Unauthorized']);
        $this->assertIsString($body);

        $response = new Response(401, [], $body);

        /** @var \PHPUnit\Framework\MockObject\MockObject&ClientInterface $mock */
        $mock = $this->clientMock;
        $mock->expects($this->once())
            ->method('sendRequest')
            ->willReturn($response);

        $client = new OpenRouter(
            $mock,
            $this->requestFactory,
            $this->streamFactory,
            $this->uriFactory,
            'sk-key',
            testMode: true
        );

        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('API request failed with status code 401');

        $client->credits();
    }
}
