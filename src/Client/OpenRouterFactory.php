<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Client;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use MichaelFrank\OpenRouter\Contracts\OpenRouterInterface;
use MichaelFrank\OpenRouter\Exceptions\ConfigurationException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * Class OpenRouterFactory
 *
 * Discovers and builds implementations of OpenRouterInterface using PSR-17/18.
 *
 * @package MichaelFrank\OpenRouter\Client
 */
final class OpenRouterFactory
{
    /**
     * Creates an OpenRouter interface client instance.
     * If no PSR dependencies are provided, autodiscovery resolves them.
     *
     * @param string $apiKey
     * @param ClientInterface|null $httpClient
     * @param RequestFactoryInterface|null $requestFactory
     * @param StreamFactoryInterface|null $streamFactory
     * @param UriFactoryInterface|null $uriFactory
     * @param string|null $siteUrl
     * @param string|null $siteName
     * @param string|null $siteCategories
     * @param string $baseUri
     * @param bool $testMode Set to true to permit local HTTP baseUri endpoints.
     * @return OpenRouterInterface
     * @throws ConfigurationException
     */
    public static function create(
        string $apiKey,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?UriFactoryInterface $uriFactory = null,
        ?string $siteUrl = null,
        ?string $siteName = null,
        ?string $siteCategories = null,
        string $baseUri = 'https://openrouter.ai/api/v1/',
        bool $testMode = false,
    ): OpenRouterInterface {
        try {
            $httpClient ??= Psr18ClientDiscovery::find();
        } catch (\Throwable $e) {
            throw new ConfigurationException(
                'Could not discover a PSR-18 HTTP client. Please install one (e.g. guzzlehttp/guzzle) or provide it explicitly.',
                null,
                $e
            );
        }

        try {
            $requestFactory ??= Psr17FactoryDiscovery::findRequestFactory();
        } catch (\Throwable $e) {
            throw new ConfigurationException(
                'Could not discover a PSR-17 request factory. Please install a factory provider (e.g. guzzlehttp/psr7) or provide it explicitly.',
                null,
                $e
            );
        }

        try {
            $streamFactory ??= Psr17FactoryDiscovery::findStreamFactory();
        } catch (\Throwable $e) {
            throw new ConfigurationException(
                'Could not discover a PSR-17 stream factory. Please install a factory provider or provide it explicitly.',
                null,
                $e
            );
        }

        try {
            $uriFactory ??= Psr17FactoryDiscovery::findUriFactory();
        } catch (\Throwable $e) {
            throw new ConfigurationException(
                'Could not discover a PSR-17 URI factory. Please install a factory provider or provide it explicitly.',
                null,
                $e
            );
        }

        return new OpenRouter(
            httpClient: $httpClient,
            requestFactory: $requestFactory,
            streamFactory: $streamFactory,
            uriFactory: $uriFactory,
            apiKey: $apiKey,
            baseUri: $baseUri,
            siteUrl: $siteUrl,
            siteName: $siteName,
            siteCategories: $siteCategories,
            testMode: $testMode
        );
    }
}
