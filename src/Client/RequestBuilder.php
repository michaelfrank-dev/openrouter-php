<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Client;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use MichaelFrank\OpenRouter\Requests\AudioSpeechRequest;
use MichaelFrank\OpenRouter\Requests\AudioTranscriptionRequest;
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\EmbeddingRequest;
use MichaelFrank\OpenRouter\Requests\ImageGenerationRequest;
use MichaelFrank\OpenRouter\Requests\ModelListQuery;
use MichaelFrank\OpenRouter\Requests\RerankRequest;

/**
 * Class RequestBuilder
 *
 * Constructs decorated PSR-7 request interfaces ready to be transmitted over HTTP.
 *
 * @package MichaelFrank\OpenRouter\Client
 */
final readonly class RequestBuilder
{
    /**
     * RequestBuilder constructor.
     *
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param UriFactoryInterface $uriFactory
     * @param string $apiKey
     * @param string $baseUri
     * @param string|null $siteUrl
     * @param string|null $siteName
     * @param string|null $siteCategories
     */
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private UriFactoryInterface $uriFactory,
        private string $apiKey,
        private string $baseUri,
        private ?string $siteUrl = null,
        private ?string $siteName = null,
        private ?string $siteCategories = null,
    ) {
    }

    /**
     * Helper to construct a generic PSR-7 request with common headers.
     *
     * @param string $method GET, POST, etc.
     * @param string $path
     * @param array<string, mixed>|null $body
     * @return RequestInterface
     */
    private function createRequest(string $method, string $path, ?array $body = null): RequestInterface
    {
        $uriString = rtrim($this->baseUri, '/') . '/' . ltrim($path, '/');
        $uri = $this->uriFactory->createUri($uriString);
        $request = $this->requestFactory->createRequest($method, $uri);

        // Standard Authorization & Content Type
        $request = $request->withHeader('Authorization', 'Bearer ' . $this->apiKey);

        if ($body !== null) {
            $request = $request->withHeader('Content-Type', 'application/json');
            $json = json_encode($body, JSON_THROW_ON_ERROR);
            $stream = $this->streamFactory->createStream($json);
            $request = $request->withBody($stream);
        }

        // Custom OpenRouter specific headers
        if ($this->siteUrl !== null && $this->siteUrl !== '') {
            $request = $request->withHeader('HTTP-Referer', $this->siteUrl);
        }
        if ($this->siteName !== null && $this->siteName !== '') {
            $request = $request->withHeader('X-OpenRouter-Title', $this->siteName);
        }
        if ($this->siteCategories !== null && $this->siteCategories !== '') {
            $request = $request->withHeader('X-OpenRouter-Categories', $this->siteCategories);
        }

        return $request;
    }

    /**
     * Builds completions request.
     *
     * @param CompletionRequest $request
     * @return RequestInterface
     */
    public function buildCompletionRequest(CompletionRequest $request): RequestInterface
    {
        return $this->createRequest('POST', 'chat/completions', $request->toArray());
    }

    /**
     * Builds embeddings request.
     *
     * @param EmbeddingRequest $request
     * @return RequestInterface
     */
    public function buildEmbeddingRequest(EmbeddingRequest $request): RequestInterface
    {
        return $this->createRequest('POST', 'embeddings', $request->toArray());
    }

    /**
     * Builds models list query request.
     *
     * @param ModelListQuery|null $query
     * @return RequestInterface
     */
    public function buildModelsRequest(?ModelListQuery $query = null): RequestInterface
    {
        $path = 'models';
        if ($query !== null) {
            $queryParams = http_build_query($query->toArray());
            if ($queryParams !== '') {
                $path .= '?' . $queryParams;
            }
        }
        return $this->createRequest('GET', $path);
    }

    /**
     * Builds generation stats details request.
     *
     * @param string $id
     * @return RequestInterface
     */
    public function buildGenerationRequest(string $id): RequestInterface
    {
        return $this->createRequest('GET', 'generation?id=' . urlencode($id));
    }

    /**
     * Builds rerank request.
     *
     * @param RerankRequest $request
     * @return RequestInterface
     */
    public function buildRerankRequest(RerankRequest $request): RequestInterface
    {
        return $this->createRequest('POST', 'rerank', $request->toArray());
    }

    /**
     * Builds audio speech synthesis request.
     *
     * @param AudioSpeechRequest $request
     * @return RequestInterface
     */
    public function buildSpeechRequest(AudioSpeechRequest $request): RequestInterface
    {
        return $this->createRequest('POST', 'audio/speech', $request->toArray());
    }

    /**
     * Builds audio transcription request.
     *
     * @param AudioTranscriptionRequest $request
     * @return RequestInterface
     */
    public function buildTranscriptionRequest(AudioTranscriptionRequest $request): RequestInterface
    {
        return $this->createRequest('POST', 'audio/transcriptions', $request->toArray());
    }

    /**
     * Builds credits checking request.
     *
     * @return RequestInterface
     */
    public function buildCreditsRequest(): RequestInterface
    {
        return $this->createRequest('GET', 'credits');
    }

    /**
     * Builds image generation request.
     *
     * @param ImageGenerationRequest $request
     * @return RequestInterface
     */
    public function buildImageRequest(ImageGenerationRequest $request): RequestInterface
    {
        return $this->createRequest('POST', 'images', $request->toArray());
    }

    /**
     * Builds request to list image generation models.
     *
     * @return RequestInterface
     */
    public function buildImageModelsRequest(): RequestInterface
    {
        return $this->createRequest('GET', 'images/models');
    }

    /**
     * Builds request to list endpoints for an image model.
     *
     * @param string $author Model author/organization
     * @param string $slug Model slug
     * @return RequestInterface
     */
    public function buildImageModelEndpointsRequest(string $author, string $slug): RequestInterface
    {
        $path = 'images/models/' . urlencode($author) . '/' . urlencode($slug) . '/endpoints';
        return $this->createRequest('GET', $path);
    }
}
