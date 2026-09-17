<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Contracts;

/**
 * Interface OpenRouterInterface
 *
 * The primary client interface for interacting with the OpenRouter API endpoints.
 *
 * @package MichaelFrank\OpenRouter\Contracts
 */
interface OpenRouterInterface
{
    /**
     * Creates a completion for the provided chat messages.
     *
     * @param \MichaelFrank\OpenRouter\Requests\CompletionRequest $request
     * @return \MichaelFrank\OpenRouter\Responses\ChatCompletionResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function completions(\MichaelFrank\OpenRouter\Requests\CompletionRequest $request): \MichaelFrank\OpenRouter\Responses\ChatCompletionResponse;

    /**
     * Creates a streaming completion for the provided chat messages, yielding chunks.
     *
     * @param \MichaelFrank\OpenRouter\Requests\CompletionRequest $request
     * @return \Generator<\MichaelFrank\OpenRouter\Responses\Streaming\CompletionStreamChunk>
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function streamCompletions(\MichaelFrank\OpenRouter\Requests\CompletionRequest $request): \Generator;

    /**
     * Generates text embeddings for the input.
     *
     * @param \MichaelFrank\OpenRouter\Requests\EmbeddingRequest $request
     * @return \MichaelFrank\OpenRouter\Responses\EmbeddingResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function embeddings(\MichaelFrank\OpenRouter\Requests\EmbeddingRequest $request): \MichaelFrank\OpenRouter\Responses\EmbeddingResponse;

    /**
     * Lists the models available on OpenRouter, optionally filtering/sorting the list.
     *
     * @param \MichaelFrank\OpenRouter\Requests\ModelListQuery|null $query
     * @return \MichaelFrank\OpenRouter\Responses\ModelListResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function models(?\MichaelFrank\OpenRouter\Requests\ModelListQuery $query = null): \MichaelFrank\OpenRouter\Responses\ModelListResponse;

    /**
     * Reranks a list of documents based on a search query.
     *
     * @param \MichaelFrank\OpenRouter\Requests\RerankRequest $request
     * @return \MichaelFrank\OpenRouter\Responses\RerankResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function rerank(\MichaelFrank\OpenRouter\Requests\RerankRequest $request): \MichaelFrank\OpenRouter\Responses\RerankResponse;

    /**
     * Synthesizes audio from text.
     *
     * @param \MichaelFrank\OpenRouter\Requests\AudioSpeechRequest $request
     * @return \MichaelFrank\OpenRouter\Responses\AudioSpeechResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function speech(\MichaelFrank\OpenRouter\Requests\AudioSpeechRequest $request): \MichaelFrank\OpenRouter\Responses\AudioSpeechResponse;

    /**
     * Transcribes audio data into text.
     *
     * @param \MichaelFrank\OpenRouter\Requests\AudioTranscriptionRequest $request
     * @return \MichaelFrank\OpenRouter\Responses\AudioTranscriptionResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function transcriptions(\MichaelFrank\OpenRouter\Requests\AudioTranscriptionRequest $request): \MichaelFrank\OpenRouter\Responses\AudioTranscriptionResponse;

    /**
     * Retrieves statistics for a completed generation.
     *
     * @param string $id
     * @return \MichaelFrank\OpenRouter\Responses\GenerationResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function generation(string $id): \MichaelFrank\OpenRouter\Responses\GenerationResponse;

    /**
     * Retrieves the current user's token credits/balance information.
     *
     * @return \MichaelFrank\OpenRouter\Responses\CreditsResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function credits(): \MichaelFrank\OpenRouter\Responses\CreditsResponse;

    /**
     * Generates an image from a text prompt.
     *
     * @param \MichaelFrank\OpenRouter\Requests\ImageGenerationRequest $request
     * @return \MichaelFrank\OpenRouter\Responses\ImageGenerationResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function images(\MichaelFrank\OpenRouter\Requests\ImageGenerationRequest $request): \MichaelFrank\OpenRouter\Responses\ImageGenerationResponse;

    /**
     * Generates an image from a text prompt, yielding streaming response chunks.
     *
     * @param \MichaelFrank\OpenRouter\Requests\ImageGenerationRequest $request
     * @return \Generator<int, \MichaelFrank\OpenRouter\Responses\ImageStreamingResponse>
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function streamImages(\MichaelFrank\OpenRouter\Requests\ImageGenerationRequest $request): \Generator;

    /**
     * Lists every image generation model with its supported-parameter superset and endpoints URL.
     *
     * @return \MichaelFrank\OpenRouter\Responses\ImageModelsListResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function imageModels(): \MichaelFrank\OpenRouter\Responses\ImageModelsListResponse;

    /**
     * Returns the full per-endpoint records for an image model: supported parameters, pricing, and allowed passthrough.
     *
     * @param string $author Model author/organization
     * @param string $slug Model slug
     * @return \MichaelFrank\OpenRouter\Responses\ImageModelEndpointsResponse
     * @throws \MichaelFrank\OpenRouter\Exceptions\OpenRouterException
     */
    public function imageModelEndpoints(string $author, string $slug): \MichaelFrank\OpenRouter\Responses\ImageModelEndpointsResponse;
}
