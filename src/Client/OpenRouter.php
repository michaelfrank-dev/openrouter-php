<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Client;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use MichaelFrank\OpenRouter\Contracts\OpenRouterInterface;
use MichaelFrank\OpenRouter\Exceptions\ApiRequestException;
use MichaelFrank\OpenRouter\Exceptions\ConfigurationException;
use MichaelFrank\OpenRouter\Exceptions\NetworkException;
use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use MichaelFrank\OpenRouter\Http\ImageSseStreamParser;
use MichaelFrank\OpenRouter\Http\JsonResponseValidator;
use MichaelFrank\OpenRouter\Http\SseStreamParser;
use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Requests\AudioSpeechRequest;
use MichaelFrank\OpenRouter\Requests\AudioTranscriptionRequest;
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\EmbeddingRequest;
use MichaelFrank\OpenRouter\Requests\ImageGenerationRequest;
use MichaelFrank\OpenRouter\Requests\ModelListQuery;
use MichaelFrank\OpenRouter\Requests\RerankRequest;
use MichaelFrank\OpenRouter\Responses\AudioSpeechResponse;
use MichaelFrank\OpenRouter\Responses\AudioTranscriptionResponse;
use MichaelFrank\OpenRouter\Responses\ChatCompletionResponse;
use MichaelFrank\OpenRouter\Responses\CreditsResponse;
use MichaelFrank\OpenRouter\Responses\EmbeddingResponse;
use MichaelFrank\OpenRouter\Responses\GenerationResponse;
use MichaelFrank\OpenRouter\Responses\ImageGenerationResponse;
use MichaelFrank\OpenRouter\Responses\ImageModelEndpointsResponse;
use MichaelFrank\OpenRouter\Responses\ImageModelsListResponse;
use MichaelFrank\OpenRouter\Responses\ImageStreamingResponse;
use MichaelFrank\OpenRouter\Responses\ModelListResponse;
use MichaelFrank\OpenRouter\Responses\RerankResponse;

/**
 * Class OpenRouter
 *
 * Primary orchestrator implementation for interacting with OpenRouter endpoints.
 *
 * @package MichaelFrank\OpenRouter\Client
 */
final class OpenRouter implements OpenRouterInterface
{
    private readonly RequestBuilder $requestBuilder;

    /**
     * OpenRouter constructor.
     *
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param UriFactoryInterface $uriFactory
     * @param string $apiKey
     * @param string $baseUri
     * @param string|null $siteUrl
     * @param string|null $siteName
     * @param string|null $siteCategories
     * @param bool $testMode Set to true to permit local non-HTTPS endpoints.
     * @throws ValidationException
     * @throws ConfigurationException
     */
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly UriFactoryInterface $uriFactory,
        private readonly string $apiKey,
        private readonly string $baseUri = 'https://openrouter.ai/api/v1/',
        private readonly ?string $siteUrl = null,
        private readonly ?string $siteName = null,
        private readonly ?string $siteCategories = null,
        private readonly bool $testMode = false,
    ) {
        if ($this->apiKey === '') {
            throw new ValidationException('OpenRouter API key cannot be empty.');
        }

        if (!$this->testMode && !str_starts_with($this->baseUri, 'https://')) {
            throw new ConfigurationException(
                'OpenRouter base URI must use HTTPS. Use test mode for local HTTP mocks.'
            );
        }

        $this->requestBuilder = new RequestBuilder(
            requestFactory: $this->requestFactory,
            streamFactory: $this->streamFactory,
            uriFactory: $this->uriFactory,
            apiKey: $this->apiKey,
            baseUri: $this->baseUri,
            siteUrl: $this->siteUrl,
            siteName: $this->siteName,
            siteCategories: $this->siteCategories
        );
    }

    /**
     * Transmits request, handles network exceptions and non-2xx status codes.
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NetworkException
     * @throws ApiRequestException
     */
    private function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (\Psr\Http\Client\ClientExceptionInterface $e) {
            $metadata = new ResponseMetadata();
            throw new NetworkException('Network communication error: ' . $e->getMessage(), $metadata, $e);
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            $metadata = ResponseMetadata::fromResponse($response);
            $body = (string)$response->getBody();
            throw new ApiRequestException(
                "API request failed with status code {$statusCode}",
                $statusCode,
                $body,
                $metadata
            );
        }

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function completions(CompletionRequest $request): ChatCompletionResponse
    {
        $psrRequest = $this->requestBuilder->buildCompletionRequest($request);
        $response = $this->sendRequest($psrRequest);

        $validator = new JsonResponseValidator();
        $payload = $validator->decode($response);

        return ChatCompletionResponse::fromArray($payload, ResponseMetadata::fromResponse($response));
    }

    /**
     * @inheritDoc
     */
    public function streamCompletions(CompletionRequest $request): \Generator
    {
        $streamOptions = $request->options->withStream(true);
        $streamRequest = new CompletionRequest(
            model: $request->model,
            messages: $request->messages,
            options: $streamOptions
        );

        $psrRequest = $this->requestBuilder->buildCompletionRequest($streamRequest);
        $response = $this->sendRequest($psrRequest);

        $parser = new SseStreamParser();
        yield from $parser->parse($response);
    }

    /**
     * @inheritDoc
     */
    public function embeddings(EmbeddingRequest $request): EmbeddingResponse
    {
        $psrRequest = $this->requestBuilder->buildEmbeddingRequest($request);
        $response = $this->sendRequest($psrRequest);

        $validator = new JsonResponseValidator();
        $payload = $validator->decode($response);

        return EmbeddingResponse::fromArray($payload, ResponseMetadata::fromResponse($response));
    }

    /**
     * @inheritDoc
     */
    public function models(?ModelListQuery $query = null): ModelListResponse
    {
        $psrRequest = $this->requestBuilder->buildModelsRequest($query);
        $response = $this->sendRequest($psrRequest);

        $validator = new JsonResponseValidator();
        $payload = $validator->decode($response);

        return ModelListResponse::fromArray($payload, ResponseMetadata::fromResponse($response));
    }

    /**
     * @inheritDoc
     */
    public function rerank(RerankRequest $request): RerankResponse
    {
        $psrRequest = $this->requestBuilder->buildRerankRequest($request);
        $response = $this->sendRequest($psrRequest);

        $validator = new JsonResponseValidator();
        $payload = $validator->decode($response);

        return RerankResponse::fromArray($payload, ResponseMetadata::fromResponse($response));
    }

    /**
     * @inheritDoc
     */
    public function speech(AudioSpeechRequest $request): AudioSpeechResponse
    {
        $psrRequest = $this->requestBuilder->buildSpeechRequest($request);
        $response = $this->sendRequest($psrRequest);

        return new AudioSpeechResponse($response->getBody(), ResponseMetadata::fromResponse($response));
    }

    /**
     * @inheritDoc
     */
    public function transcriptions(AudioTranscriptionRequest $request): AudioTranscriptionResponse
    {
        $psrRequest = $this->requestBuilder->buildTranscriptionRequest($request);
        $response = $this->sendRequest($psrRequest);

        $validator = new JsonResponseValidator();
        $payload = $validator->decode($response);

        return AudioTranscriptionResponse::fromArray($payload, ResponseMetadata::fromResponse($response));
    }

    /**
     * @inheritDoc
     */
    public function generation(string $id): GenerationResponse
    {
        $psrRequest = $this->requestBuilder->buildGenerationRequest($id);
        $response = $this->sendRequest($psrRequest);

        $validator = new JsonResponseValidator();
        $payload = $validator->decode($response);

        return GenerationResponse::fromArray($payload, ResponseMetadata::fromResponse($response));
    }

    /**
     * @inheritDoc
     */
    public function credits(): CreditsResponse
    {
        $psrRequest = $this->requestBuilder->buildCreditsRequest();
        $response = $this->sendRequest($psrRequest);

        $validator = new JsonResponseValidator();
        $payload = $validator->decode($response);

        return CreditsResponse::fromArray($payload, ResponseMetadata::fromResponse($response));
    }

    /**
     * @inheritDoc
     */
    public function images(ImageGenerationRequest $request): ImageGenerationResponse
    {
        $psrRequest = $this->requestBuilder->buildImageRequest($request);
        $response = $this->sendRequest($psrRequest);

        $validator = new JsonResponseValidator();
        $payload = $validator->decode($response);

        return ImageGenerationResponse::fromArray($payload, ResponseMetadata::fromResponse($response));
    }

    /**
     * @inheritDoc
     */
    public function streamImages(ImageGenerationRequest $request): \Generator
    {
        $streamRequest = $request->withStream(true);
        $psrRequest = $this->requestBuilder->buildImageRequest($streamRequest);
        $response = $this->sendRequest($psrRequest);

        $parser = new ImageSseStreamParser();
        yield from $parser->parse($response);
    }

    /**
     * @inheritDoc
     */
    public function imageModels(): ImageModelsListResponse
    {
        $psrRequest = $this->requestBuilder->buildImageModelsRequest();
        $response = $this->sendRequest($psrRequest);

        $validator = new JsonResponseValidator();
        $payload = $validator->decode($response);

        return ImageModelsListResponse::fromArray($payload, ResponseMetadata::fromResponse($response));
    }

    /**
     * @inheritDoc
     */
    public function imageModelEndpoints(string $author, string $slug): ImageModelEndpointsResponse
    {
        if ($author === '') {
            throw new ValidationException('Model author cannot be empty.');
        }
        if ($slug === '') {
            throw new ValidationException('Model slug cannot be empty.');
        }

        $psrRequest = $this->requestBuilder->buildImageModelEndpointsRequest($author, $slug);
        $response = $this->sendRequest($psrRequest);

        $validator = new JsonResponseValidator();
        $payload = $validator->decode($response);

        return ImageModelEndpointsResponse::fromArray($payload, ResponseMetadata::fromResponse($response));
    }
}
