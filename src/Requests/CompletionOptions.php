<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

use MichaelFrank\OpenRouter\Enums\Verbosity;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\SearchContextSize;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\WebSearchEngine;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\WebSearchOptions;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\XSearchFilter;
use MichaelFrank\OpenRouter\Requests\ResponseFormat\JsonSchemaResponseFormat;
use MichaelFrank\OpenRouter\Requests\ResponseFormat\ResponseFormat;
use MichaelFrank\OpenRouter\Requests\Tools\OpenRouterWebSearchTool;
use MichaelFrank\OpenRouter\Requests\Tools\ServerTool;
use MichaelFrank\OpenRouter\Requests\Tools\ToolDefinition;

/**
 * Class CompletionOptions
 *
 * Configures additional generation parameters for chat completions.
 * This class is strictly immutable.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final class CompletionOptions implements \JsonSerializable
{
    /**
     * CompletionOptions constructor.
     *
     * @param float|null $temperature
     * @param float|null $topP
     * @param float|null $topK
     * @param float|null $minP
     * @param float|null $topA
     * @param int|null $seed
     * @param int|null $maxTokens
     * @param array<string>|null $stop
     * @param float|null $repetitionPenalty
     * @param float|null $presencePenalty
     * @param float|null $frequencyPenalty
     * @param array<ServerTool|ToolDefinition>|null $tools
     * @param string|array<string, mixed>|null $toolChoice
     * @param bool $parallelToolCalls
     * @param ProviderPreferences|null $provider
     * @param string|null $route
     * @param array<string>|null $transforms
     * @param array<string|array<string, mixed>>|null $plugins
     * @param WebSearchOptions|null $webSearchOptions
     * @param XSearchFilter|null $xSearchFilter
     * @param bool $stream
     * @param ResponseFormat|null $responseFormat
     * @param Verbosity|null $verbosity
     * @param bool $includeReasoning
     * @param bool|null $responseHealing
     * @param array<string, float>|null $logitBias
     * @param array<string, mixed>|null $prediction
     * @param bool|null $logprobs
     * @param int|null $topLogprobs
     * @param string|null $user
     */
    public function __construct(
        public readonly ?float $temperature = null,
        public readonly ?float $topP = null,
        public readonly ?float $topK = null,
        public readonly ?float $minP = null,
        public readonly ?float $topA = null,
        public readonly ?int $seed = null,
        public readonly ?int $maxTokens = null,
        public readonly ?array $stop = null,
        public readonly ?float $repetitionPenalty = null,
        public readonly ?float $presencePenalty = null,
        public readonly ?float $frequencyPenalty = null,
        public readonly ?array $tools = null,
        public readonly string|array|null $toolChoice = null,
        public readonly bool $parallelToolCalls = true,
        public readonly ?ProviderPreferences $provider = null,
        public readonly ?string $route = null,
        public readonly ?array $transforms = null,
        public readonly ?array $plugins = null,
        public readonly ?WebSearchOptions $webSearchOptions = null,
        public readonly ?XSearchFilter $xSearchFilter = null,
        public readonly bool $stream = false,
        public readonly ?ResponseFormat $responseFormat = null,
        public readonly ?Verbosity $verbosity = null,
        public readonly bool $includeReasoning = false,
        public readonly ?bool $responseHealing = null,
        public readonly ?array $logitBias = null,
        public readonly ?array $prediction = null,
        public readonly ?bool $logprobs = null,
        public readonly ?int $topLogprobs = null,
        public readonly ?string $user = null,
    ) {
    }

    /**
     * Helper to load options from raw associative array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $temperature = $data['temperature'] ?? null;
        $topP = $data['top_p'] ?? null;
        $topK = $data['top_k'] ?? null;
        $minP = $data['min_p'] ?? null;
        $topA = $data['top_a'] ?? null;
        $seed = $data['seed'] ?? null;
        $maxTokens = $data['max_tokens'] ?? null;
        $repetitionPenalty = $data['repetition_penalty'] ?? null;
        $presencePenalty = $data['presence_penalty'] ?? null;
        $frequencyPenalty = $data['frequency_penalty'] ?? null;
        $stream = (bool)($data['stream'] ?? false);
        $verbosity = $data['verbosity'] ?? null;
        $route = $data['route'] ?? null;
        $user = $data['user'] ?? null;
        $logprobs = $data['logprobs'] ?? null;
        $topLogprobs = $data['top_logprobs'] ?? null;
        $responseHealing = $data['response_healing'] ?? null;
        $parallelToolCalls = (bool)($data['parallel_tool_calls'] ?? true);
        $includeReasoning = (bool)($data['include_reasoning'] ?? false);

        $stop = null;
        if (isset($data['stop']) && is_array($data['stop'])) {
            $stop = [];
            foreach ($data['stop'] as $s) {
                if (is_string($s)) {
                    $stop[] = $s;
                }
            }
        }

        $tools = null;
        if (isset($data['tools']) && is_array($data['tools'])) {
            $tools = [];
            foreach ($data['tools'] as $tool) {
                if ($tool instanceof ServerTool || $tool instanceof ToolDefinition) {
                    $tools[] = $tool;
                }
            }
        }

        $toolChoice = null;
        if (isset($data['tool_choice'])) {
            $rawToolChoice = $data['tool_choice'];
            if (is_string($rawToolChoice)) {
                $toolChoice = $rawToolChoice;
            } elseif (is_array($rawToolChoice)) {
                $choiceArr = [];
                foreach ($rawToolChoice as $k => $v) {
                    if (is_string($k)) {
                        $choiceArr[$k] = $v;
                    }
                }
                $toolChoice = $choiceArr;
            }
        }

        $logitBias = null;
        if (isset($data['logit_bias']) && is_array($data['logit_bias'])) {
            $logitBias = [];
            foreach ($data['logit_bias'] as $k => $v) {
                if (is_string($k) && is_numeric($v)) {
                    $logitBias[$k] = (float)$v;
                }
            }
        }

        $prediction = null;
        if (isset($data['prediction']) && is_array($data['prediction'])) {
            $prediction = [];
            foreach ($data['prediction'] as $k => $v) {
                if (is_string($k)) {
                    $prediction[$k] = $v;
                }
            }
        }

        $transforms = null;
        if (isset($data['transforms']) && is_array($data['transforms'])) {
            $transforms = [];
            foreach ($data['transforms'] as $t) {
                if (is_string($t)) {
                    $transforms[] = $t;
                }
            }
        }

        $plugins = null;
        if (isset($data['plugins']) && is_array($data['plugins'])) {
            $plugins = [];
            foreach ($data['plugins'] as $p) {
                if (is_string($p) || is_array($p)) {
                    $plugins[] = $p;
                }
            }
        }

        $webSearchOptions = null;
        if (isset($data['web_search_options'])) {
            if ($data['web_search_options'] instanceof WebSearchOptions) {
                $webSearchOptions = $data['web_search_options'];
            } elseif (is_array($data['web_search_options'])) {
                $searchContextSize = null;
                if (isset($data['web_search_options']['search_context_size'])) {
                    $searchContextSize = SearchContextSize::fromString($data['web_search_options']['search_context_size']);
                }
                $webSearchOptions = new WebSearchOptions($searchContextSize);
            }
        }

        $xSearchFilter = null;
        if (isset($data['x_search_filter'])) {
            if ($data['x_search_filter'] instanceof XSearchFilter) {
                $xSearchFilter = $data['x_search_filter'];
            } elseif (is_array($data['x_search_filter'])) {
                $xSearchFilter = new XSearchFilter(
                    allowedXHandles: $data['x_search_filter']['allowed_x_handles'] ?? null,
                    excludedXHandles: $data['x_search_filter']['excluded_x_handles'] ?? null,
                    fromDate: $data['x_search_filter']['from_date'] ?? null,
                    toDate: $data['x_search_filter']['to_date'] ?? null,
                    enableImageUnderstanding: $data['x_search_filter']['enable_image_understanding'] ?? null,
                    enableVideoUnderstanding: $data['x_search_filter']['enable_video_understanding'] ?? null,
                );
            }
        }

        return new self(
            temperature: is_numeric($temperature) ? (float)$temperature : null,
            topP: is_numeric($topP) ? (float)$topP : null,
            topK: is_numeric($topK) ? (float)$topK : null,
            minP: is_numeric($minP) ? (float)$minP : null,
            topA: is_numeric($topA) ? (float)$topA : null,
            seed: is_numeric($seed) ? (int)$seed : null,
            maxTokens: is_numeric($maxTokens) ? (int)$maxTokens : null,
            stop: $stop,
            repetitionPenalty: is_numeric($repetitionPenalty) ? (float)$repetitionPenalty : null,
            presencePenalty: is_numeric($presencePenalty) ? (float)$presencePenalty : null,
            frequencyPenalty: is_numeric($frequencyPenalty) ? (float)$frequencyPenalty : null,
            tools: $tools,
            toolChoice: $toolChoice,
            parallelToolCalls: $parallelToolCalls,
            provider: isset($data['provider']) && $data['provider'] instanceof ProviderPreferences ? $data['provider'] : null,
            route: is_string($route) ? $route : null,
            transforms: $transforms,
            plugins: $plugins,
            webSearchOptions: $webSearchOptions,
            xSearchFilter: $xSearchFilter,
            stream: $stream,
            responseFormat: isset($data['response_format']) && $data['response_format'] instanceof ResponseFormat ? $data['response_format'] : null,
            verbosity: is_string($verbosity) ? Verbosity::fromString($verbosity) : null,
            includeReasoning: $includeReasoning,
            responseHealing: $responseHealing !== null ? (bool)$responseHealing : null,
            logitBias: $logitBias,
            prediction: $prediction,
            logprobs: $logprobs !== null ? (bool)$logprobs : null,
            topLogprobs: is_numeric($topLogprobs) ? (int)$topLogprobs : null,
            user: is_string($user) ? $user : null,
        );
    }

    /**
     * Returns a new instance with the stream flag updated.
     *
     * @param bool $stream
     * @return self
     */
    public function withStream(bool $stream = true): self
    {
        return new self(
            temperature: $this->temperature,
            topP: $this->topP,
            topK: $this->topK,
            minP: $this->minP,
            topA: $this->topA,
            seed: $this->seed,
            maxTokens: $this->maxTokens,
            stop: $this->stop,
            repetitionPenalty: $this->repetitionPenalty,
            presencePenalty: $this->presencePenalty,
            frequencyPenalty: $this->frequencyPenalty,
            tools: $this->tools,
            toolChoice: $this->toolChoice,
            parallelToolCalls: $this->parallelToolCalls,
            provider: $this->provider,
            route: $this->route,
            transforms: $this->transforms,
            plugins: $this->plugins,
            webSearchOptions: $this->webSearchOptions,
            xSearchFilter: $this->xSearchFilter,
            stream: $stream,
            responseFormat: $this->responseFormat,
            verbosity: $this->verbosity,
            includeReasoning: $this->includeReasoning,
            responseHealing: $this->responseHealing,
            logitBias: $this->logitBias,
            prediction: $this->prediction,
            logprobs: $this->logprobs,
            topLogprobs: $this->topLogprobs,
            user: $this->user,
        );
    }

    /**
     * Returns a new instance with a structured output configuration schema.
     *
     * @param string $name
     * @param array<string, mixed> $schema
     * @param bool $strict
     * @return self
     */
    public function withStructuredOutput(string $name, array $schema, bool $strict = true): self
    {
        return new self(
            temperature: $this->temperature,
            topP: $this->topP,
            topK: $this->topK,
            minP: $this->minP,
            topA: $this->topA,
            seed: $this->seed,
            maxTokens: $this->maxTokens,
            stop: $this->stop,
            repetitionPenalty: $this->repetitionPenalty,
            presencePenalty: $this->presencePenalty,
            frequencyPenalty: $this->frequencyPenalty,
            tools: $this->tools,
            toolChoice: $this->toolChoice,
            parallelToolCalls: $this->parallelToolCalls,
            provider: $this->provider,
            route: $this->route,
            transforms: $this->transforms,
            plugins: $this->plugins,
            webSearchOptions: $this->webSearchOptions,
            xSearchFilter: $this->xSearchFilter,
            stream: $this->stream,
            responseFormat: new JsonSchemaResponseFormat($name, $schema, $strict),
            verbosity: $this->verbosity,
            includeReasoning: $this->includeReasoning,
            responseHealing: $this->responseHealing,
            logitBias: $this->logitBias,
            prediction: $this->prediction,
            logprobs: $this->logprobs,
            topLogprobs: $this->topLogprobs,
            user: $this->user,
        );
    }

    /**
     * Returns a new instance with web search enabled.
     *
     * @deprecated Use the openrouter:web_search server tool instead.
     * @param int $maxResults
     * @param string|null $searchPrompt
     * @return self
     */
    public function withWebSearch(int $maxResults = 5, ?string $searchPrompt = null): self
    {
        $currentTools = $this->tools ?? [];
        $currentTools[] = new OpenRouterWebSearchTool(maxResults: $maxResults);

        return new self(
            temperature: $this->temperature,
            topP: $this->topP,
            topK: $this->topK,
            minP: $this->minP,
            topA: $this->topA,
            seed: $this->seed,
            maxTokens: $this->maxTokens,
            stop: $this->stop,
            repetitionPenalty: $this->repetitionPenalty,
            presencePenalty: $this->presencePenalty,
            frequencyPenalty: $this->frequencyPenalty,
            tools: $currentTools,
            toolChoice: $this->toolChoice,
            parallelToolCalls: $this->parallelToolCalls,
            provider: $this->provider,
            route: $this->route,
            transforms: $this->transforms,
            plugins: $this->plugins,
            webSearchOptions: $this->webSearchOptions,
            xSearchFilter: $this->xSearchFilter,
            stream: $this->stream,
            responseFormat: $this->responseFormat,
            verbosity: $this->verbosity,
            includeReasoning: $this->includeReasoning,
            responseHealing: $this->responseHealing,
            logitBias: $this->logitBias,
            prediction: $this->prediction,
            logprobs: $this->logprobs,
            topLogprobs: $this->topLogprobs,
            user: $this->user,
        );
    }

    /**
     * Returns a new instance with response healing enabled.
     *
     * @deprecated Use the openrouter:response_healing server tool instead.
     * @return self
     */
    public function withResponseHealing(): self
    {
        return new self(
            temperature: $this->temperature,
            topP: $this->topP,
            topK: $this->topK,
            minP: $this->minP,
            topA: $this->topA,
            seed: $this->seed,
            maxTokens: $this->maxTokens,
            stop: $this->stop,
            repetitionPenalty: $this->repetitionPenalty,
            presencePenalty: $this->presencePenalty,
            frequencyPenalty: $this->frequencyPenalty,
            tools: $this->tools,
            toolChoice: $this->toolChoice,
            parallelToolCalls: $this->parallelToolCalls,
            provider: $this->provider,
            route: $this->route,
            transforms: $this->transforms,
            plugins: $this->plugins,
            webSearchOptions: $this->webSearchOptions,
            xSearchFilter: $this->xSearchFilter,
            stream: $this->stream,
            responseFormat: $this->responseFormat,
            verbosity: $this->verbosity,
            includeReasoning: $this->includeReasoning,
            responseHealing: true,
            logitBias: $this->logitBias,
            prediction: $this->prediction,
            logprobs: $this->logprobs,
            topLogprobs: $this->topLogprobs,
            user: $this->user,
        );
    }

    /**
     * Returns a new instance with provider preferences updated.
     *
     * @param ProviderPreferences $preferences
     * @return self
     */
    public function withProviderPreferences(ProviderPreferences $preferences): self
    {
        return new self(
            temperature: $this->temperature,
            topP: $this->topP,
            topK: $this->topK,
            minP: $this->minP,
            topA: $this->topA,
            seed: $this->seed,
            maxTokens: $this->maxTokens,
            stop: $this->stop,
            repetitionPenalty: $this->repetitionPenalty,
            presencePenalty: $this->presencePenalty,
            frequencyPenalty: $this->frequencyPenalty,
            tools: $this->tools,
            toolChoice: $this->toolChoice,
            parallelToolCalls: $this->parallelToolCalls,
            provider: $preferences,
            route: $this->route,
            transforms: $this->transforms,
            plugins: $this->plugins,
            webSearchOptions: $this->webSearchOptions,
            xSearchFilter: $this->xSearchFilter,
            stream: $this->stream,
            responseFormat: $this->responseFormat,
            verbosity: $this->verbosity,
            includeReasoning: $this->includeReasoning,
            responseHealing: $this->responseHealing,
            logitBias: $this->logitBias,
            prediction: $this->prediction,
            logprobs: $this->logprobs,
            topLogprobs: $this->topLogprobs,
            user: $this->user,
        );
    }

    /**
     * Returns a new instance with web search options configured.
     *
     * @param WebSearchOptions $webSearchOptions
     * @return self
     */
    public function withWebSearchOptions(WebSearchOptions $webSearchOptions): self
    {
        return new self(
            temperature: $this->temperature,
            topP: $this->topP,
            topK: $this->topK,
            minP: $this->minP,
            topA: $this->topA,
            seed: $this->seed,
            maxTokens: $this->maxTokens,
            stop: $this->stop,
            repetitionPenalty: $this->repetitionPenalty,
            presencePenalty: $this->presencePenalty,
            frequencyPenalty: $this->frequencyPenalty,
            tools: $this->tools,
            toolChoice: $this->toolChoice,
            parallelToolCalls: $this->parallelToolCalls,
            provider: $this->provider,
            route: $this->route,
            transforms: $this->transforms,
            plugins: $this->plugins,
            webSearchOptions: $webSearchOptions,
            xSearchFilter: $this->xSearchFilter,
            stream: $this->stream,
            responseFormat: $this->responseFormat,
            verbosity: $this->verbosity,
            includeReasoning: $this->includeReasoning,
            responseHealing: $this->responseHealing,
            logitBias: $this->logitBias,
            prediction: $this->prediction,
            logprobs: $this->logprobs,
            topLogprobs: $this->topLogprobs,
            user: $this->user,
        );
    }

    /**
     * Returns a new instance with X search filter configured.
     *
     * @param XSearchFilter $xSearchFilter
     * @return self
     */
    public function withXSearchFilter(XSearchFilter $xSearchFilter): self
    {
        return new self(
            temperature: $this->temperature,
            topP: $this->topP,
            topK: $this->topK,
            minP: $this->minP,
            topA: $this->topA,
            seed: $this->seed,
            maxTokens: $this->maxTokens,
            stop: $this->stop,
            repetitionPenalty: $this->repetitionPenalty,
            presencePenalty: $this->presencePenalty,
            frequencyPenalty: $this->frequencyPenalty,
            tools: $this->tools,
            toolChoice: $this->toolChoice,
            parallelToolCalls: $this->parallelToolCalls,
            provider: $this->provider,
            route: $this->route,
            transforms: $this->transforms,
            plugins: $this->plugins,
            webSearchOptions: $this->webSearchOptions,
            xSearchFilter: $xSearchFilter,
            stream: $this->stream,
            responseFormat: $this->responseFormat,
            verbosity: $this->verbosity,
            includeReasoning: $this->includeReasoning,
            responseHealing: $this->responseHealing,
            logitBias: $this->logitBias,
            prediction: $this->prediction,
            logprobs: $this->logprobs,
            topLogprobs: $this->topLogprobs,
            user: $this->user,
        );
    }

    /**
     * Returns a new instance with plugins configured.
     *
     * @param array<string|array<string, mixed>> $plugins
     * @return self
     */
    public function withPlugins(array $plugins): self
    {
        return new self(
            temperature: $this->temperature,
            topP: $this->topP,
            topK: $this->topK,
            minP: $this->minP,
            topA: $this->topA,
            seed: $this->seed,
            maxTokens: $this->maxTokens,
            stop: $this->stop,
            repetitionPenalty: $this->repetitionPenalty,
            presencePenalty: $this->presencePenalty,
            frequencyPenalty: $this->frequencyPenalty,
            tools: $this->tools,
            toolChoice: $this->toolChoice,
            parallelToolCalls: $this->parallelToolCalls,
            provider: $this->provider,
            route: $this->route,
            transforms: $this->transforms,
            plugins: $plugins,
            webSearchOptions: $this->webSearchOptions,
            xSearchFilter: $this->xSearchFilter,
            stream: $this->stream,
            responseFormat: $this->responseFormat,
            verbosity: $this->verbosity,
            includeReasoning: $this->includeReasoning,
            responseHealing: $this->responseHealing,
            logitBias: $this->logitBias,
            prediction: $this->prediction,
            logprobs: $this->logprobs,
            topLogprobs: $this->topLogprobs,
            user: $this->user,
        );
    }

    /**
     * Converts properties to serializable array following endpoint schemas.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        $fields = [
            'temperature' => $this->temperature,
            'top_p' => $this->topP,
            'top_k' => $this->topK,
            'min_p' => $this->minP,
            'top_a' => $this->topA,
            'seed' => $this->seed,
            'max_tokens' => $this->maxTokens,
            'stop' => $this->stop,
            'repetition_penalty' => $this->repetitionPenalty,
            'presence_penalty' => $this->presencePenalty,
            'frequency_penalty' => $this->frequencyPenalty,
            'tool_choice' => $this->toolChoice,
            'parallel_tool_calls' => $this->parallelToolCalls,
            'provider' => $this->provider?->toArray(),
            'route' => $this->route,
            'transforms' => $this->transforms,
            'plugins' => $this->plugins,
            'web_search_options' => $this->webSearchOptions?->toArray(),
            'x_search_filter' => $this->xSearchFilter?->toArray(),
            'stream' => $this->stream,
            'response_format' => $this->responseFormat?->toArray(),
            'verbosity' => $this->verbosity?->value,
            'include_reasoning' => $this->includeReasoning,
            'response_healing' => $this->responseHealing,
            'logit_bias' => $this->logitBias,
            'prediction' => $this->prediction,
            'logprobs' => $this->logprobs,
            'top_logprobs' => $this->topLogprobs,
            'user' => $this->user,
        ];

        foreach ($fields as $key => $val) {
            if ($val !== null) {
                if (is_array($val) && $val === []) {
                    continue;
                }
                $data[$key] = $val;
            }
        }

        if ($this->tools !== null && $this->tools !== []) {
            $data['tools'] = array_map(
                static fn(ServerTool|ToolDefinition $tool): array => $tool->toArray(),
                $this->tools
            );
        }

        return $data;
    }

    /**
     * JSON serialization format.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
