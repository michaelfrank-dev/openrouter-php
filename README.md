# OpenRouter PHP SDK

> [!WARNING]
> This is an *unofficial*, community-maintained PHP SDK for the OpenRouter API. It is not maintained by OpenRouter.

A framework-agnostic, strictly typed PHP SDK for the OpenRouter API. Fully compliant with PSR-12 coding standards, leveraging PSR-18 HTTP Client and PSR-17 HTTP Factory standards for interoperability.

> [!NOTE]
> This SDK does not yet have all OpenRouter endpoints and options implemented. Currently implemented features and endpoints include:
> - **Chat Completions:** Standard completions (`completions`) and real-time SSE streaming (`streamCompletions`).
> - **Provider Routing & Fallbacks:** Model fallbacks, provider sorting (price, throughput, latency), percentile cutoffs, and max pricing.
> - **Server-Side Tools:** Model-driven Web Search (`OpenRouterWebSearchTool`), Web Fetch (`OpenRouterWebFetchTool`), Image Generation (`OpenRouterImageGenerationTool`), and SpaceXAI X Search (`SpaceXXSearchTool`).
> - **Routing-Level Grounding & Filters:** Model-agnostic web search options (`WebSearchOptions`) and X/Twitter search filtering (`XSearchFilter`).
> - **Client-Side Tool Calling:** Function definitions (`ToolDefinition`) and multi-turn tool message handling.
> - **Models & Endpoints Discovery:** Listing and querying models (`models`), image generation models catalog (`imageModels`), and per-endpoint capabilities & pricing (`imageModelEndpoints`).
> - **Text Embeddings:** Generating vector embeddings (`embeddings`).
> - **Document Reranking:** Scoring and reordering documents based on search queries (`rerank`).
> - **Audio (Speech & Transcriptions):** Text-to-speech synthesis with direct file export (`speech`) and audio file transcription (`transcriptions`).
> - **Image Generation:** Standalone text-to-image generation (`images`) and streaming generation events (`streamImages`).
> - **Account & Generation Analytics:** Checking credit balance (`credits`) and looking up generation stats by ID (`generation`).
> - **Response Metadata & Rate Limits:** Automatic extraction of request IDs and rate limit headers (`X-RateLimit-*`).

---

## Features

- Pure PSR-18 / PSR-17 Dependency Injection
- Static analysis typing (PHPStan level 9, Psalm level 1)
- Request-side extensible wrappers (no hardcoded enums for API-extensible keys)
- Immutable option and request configurations
- Fully supports completions, streaming, embeddings, reranking, speech, transcriptions, image generation, and key checking
- OpenRouter-native features: routing preferences, fallback models, server tools (Web Search & Fetch)
- Strict client-side request validation (e.g., verifying mutually exclusive search engine constraints)
- Built-in Response Metadata & Rate limit tracking

---

## Installation

Install the package via Composer:

```bash
composer require michaelfrank-dev/openrouter-php
```

Make sure you have a PSR-18 HTTP client (like Guzzle) and PSR-17 factories installed:

```bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

---

## Usage Examples

> [!TIP]
> For local development and testing, create a dedicated API key with a low credit/spending limit in your [OpenRouter Key Settings](https://openrouter.ai/settings/keys) to limit financial exposure.

### Initialization via Discovery

```php
use MichaelFrank\OpenRouter\Client\OpenRouterFactory;

// Automatically discovers PSR-18 and PSR-17 implementations
$client = OpenRouterFactory::create(
    apiKey: $_ENV['OPENROUTER_API_KEY'],
    siteUrl: 'https://myapp.com',
    siteName: 'My Application'
);
```

### Provider Sort by Price

```php
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\CompletionOptions;
use MichaelFrank\OpenRouter\Requests\Messages\UserMessage;
use MichaelFrank\OpenRouter\Requests\ProviderPreferences;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\ProviderSort;

$request = new CompletionRequest(
    model: '~openai/gpt-mini-latest',
    messages: [new UserMessage('Write a short poem about Rio de Janeiro.')],
    options: (new CompletionOptions())
        ->withProviderPreferences(
            new ProviderPreferences(sort: new ProviderSort(ProviderSort::PRICE))
        ),
);

$response = $client->completions($request);
echo $response->getFirstChoice()->message->content;
```

### Latency/Throughput Constraints and Max Price

```php
use MichaelFrank\OpenRouter\Requests\ProviderPreferences;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\PercentileCutoffs;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\ProviderSort;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\ProviderSortConfig;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\ProviderSortPartition;

$provider = new ProviderPreferences(
    order: ['anthropic', 'openai'],
    sort: new ProviderSortConfig(
        by: new ProviderSort(ProviderSort::THROUGHPUT),
        partition: new ProviderSortPartition(ProviderSortPartition::MODEL),
    ),
    preferredMinThroughput: new PercentileCutoffs(p50: 100.0),
    preferredMaxLatency: new PercentileCutoffs(p50: 0.5, p99: 2.0),
    maxPrice: ['prompt' => '0.55', 'completion' => '5.65'],
);
```

### Fallback / Multi-model Routing

```php
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\Messages\UserMessage;

$request = new CompletionRequest(
    model: ['~openai/gpt-mini-latest', '~anthropic/claude-haiku-latest'],
    messages: [new UserMessage('How is it going?')],
);
$response = $client->completions($request);
```

### Filtering and Querying Models list

```php
use MichaelFrank\OpenRouter\Requests\ModelListQuery;
use MichaelFrank\OpenRouter\Requests\ModelListSort;

$models = $client->models(
    new ModelListQuery(
        sort: new ModelListSort(ModelListSort::THROUGHPUT_HIGH_TO_LOW),
        outputModalities: 'text',
    ),
);
```

### Server-Side Tools (Web Search, Fetch & Image Generation)

OpenRouter supports server-side tools that allow the model to interact directly with web content. 

> [!NOTE]
> **Key Difference between Server-Side Tools (6a) and Routing Options (6b):**
> * **Server-Side Tools (6a)** are **agentic**. The model decides dynamically during generation if and when it needs to run a web search or fetch a URL, constructing the query or URL based on context.
> * **Routing-Level Grounding & Filters (6b)** are **pre-configured**. They pre-inject search context before the model starts generating, or apply static platform-wide filters (like X/Twitter filtering constraints for Grok) without requiring tool-calling logic from the model.

#### Web Search Server Tool

Pass `OpenRouterWebSearchTool` to give the model real-time search capabilities. It supports customizable parameters for engines like Exa, Parallel, Perplexity, or native provider search:

```php
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\CompletionOptions;
use MichaelFrank\OpenRouter\Requests\Messages\UserMessage;
use MichaelFrank\OpenRouter\Requests\Tools\OpenRouterWebSearchTool;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\SearchContextSize;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\WebSearchEngine;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\UserLocation;

$request = new CompletionRequest(
    model: '~anthropic/claude-haiku-latest',
    messages: [new UserMessage('Historical landmarks and tourist sites to visit in Recife')],
    options: new CompletionOptions(
        tools: [
            new OpenRouterWebSearchTool(
                engine: new WebSearchEngine(WebSearchEngine::EXA), // exa, auto, native, firecrawl, parallel, perplexity
                maxResults: 5,                                     // results per search call
                maxTotalResults: 15,                               // max cumulative results across searches
                searchContextSize: new SearchContextSize(SearchContextSize::MEDIUM), // low, medium, high
                maxCharacters: 2000,                               // exact max characters of content per result
                allowedDomains: ['wikipedia.org', 'reddit.com'],   // restrict search to these domains
                excludedDomains: ['pinterest.com'],                   // exclude these domains
                userLocation: new UserLocation(                    // geographic bias for search results (does not inject context into reasoning)
                    city: 'Belo Horizonte',
                    region: 'Minas Gerais',
                    country: 'Brazil',
                    timezone: 'America/Sao_Paulo'
                ),
                maxUses: 3,                                        // cap the number of searches model can perform
                xSearch: true                                      // enable X/Twitter search for SpaceXAI/Grok (or pass XSearchFilter)
            )
        ]
    )
);

$response = $client->completions($request);
```

> [!NOTE]
> The `userLocation` parameter only geographically biases the search engine results (currently supported by native provider search). It does not inject location context into the model's reasoning.

> [!IMPORTANT]
> **Domain Filtering Engine Compatibility:**
> - **Exa:** Supports specifying both `allowedDomains` and `excludedDomains` simultaneously.
> - **Parallel, Firecrawl, and Perplexity:** Treat `allowedDomains` and `excludedDomains` as **mutually exclusive**. The SDK throws a `ValidationException` if you attempt to specify both in the same request.
> - **Native:** Support depends on the specific provider (many native provider search models do not support domain filtering).

#### Web Fetch Server Tool

Pass `OpenRouterWebFetchTool` to fetch the contents of a specific URL directly during completion:

```php
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\CompletionOptions;
use MichaelFrank\OpenRouter\Requests\Messages\UserMessage;
use MichaelFrank\OpenRouter\Requests\Tools\OpenRouterWebFetchTool;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\WebFetchEngine;

$request = new CompletionRequest(
    model: '~openai/gpt-mini-latest',
    messages: [new UserMessage('Analyze the content on this webpage.')],
    options: new CompletionOptions(
        tools: [
            new OpenRouterWebFetchTool(
                engine: new WebFetchEngine(WebFetchEngine::AUTO),
                url: 'https://example.com',
                maxContentTokens: 1000,
                allowedDomains: ['example.com'],
                blockedDomains: ['irrelevant-site.com'],           // filter out domains not relevant to the search
                maxUses: 2
            )
        ]
    )
);

$response = $client->completions($request);
```

> [!NOTE]
> For the Web Fetch tool, `allowedDomains` restricts URL retrieval to only those specific domains, while `blockedDomains` explicitly rejects matching URLs. Both parameters can be defined simultaneously to establish precise boundary controls.



#### Image Generation Server Tool

Pass `OpenRouterImageGenerationTool` to allow any supported model to dynamically generate images during completion:

```php
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\CompletionOptions;
use MichaelFrank\OpenRouter\Requests\Messages\UserMessage;
use MichaelFrank\OpenRouter\Requests\Tools\OpenRouterImageGenerationTool;
use MichaelFrank\OpenRouter\Enums\ImageQuality;
use MichaelFrank\OpenRouter\Enums\ImageBackground;
use MichaelFrank\OpenRouter\Enums\ImageOutputFormat;

$request = new CompletionRequest(
    model: 'openai/gpt-5.2',
    messages: [new UserMessage('Create an image of a futuristic city at sunset')],
    options: new CompletionOptions(
        tools: [
            new OpenRouterImageGenerationTool(
                model: 'openai/gpt-image-2', // which image generation model to use
                quality: ImageQuality::High,  // Auto, Low, Medium, High
                aspectRatio: '16:9',
                size: '1024x1024',
                background: ImageBackground::Transparent, // Auto, Transparent, Opaque
                outputFormat: ImageOutputFormat::Png, // Png, Jpeg, Webp, Svg
                outputCompression: 85,
                moderation: 'auto'
            )
        ]
    )
);

$response = $client->completions($request);
```

### Model-Agnostic Web Search Options & Filters (Routing Level)

For native provider grounding/web search context size control:

```php
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\CompletionOptions;
use MichaelFrank\OpenRouter\Requests\Messages\UserMessage;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\SearchContextSize;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\WebSearchOptions;

$request = new CompletionRequest(
    model: '~deepseek/deepseek-v4-flash-latest',
    messages: [new UserMessage('What are the latest developments in quantum computing?')],
    options: (new CompletionOptions())
        ->withWebSearchOptions(
            new WebSearchOptions(new SearchContextSize(SearchContextSize::LOW))
        )
);

$response = $client->completions($request);
```

#### X/Twitter Search with SpaceXAI Grok Models

To enable X/Twitter search for SpaceXAI Grok models, configure `xSearch` on `OpenRouterWebSearchTool`. You can enable X Search with no filters (using `true` or `new XSearchFilter()`), or specify filters (`allowedXHandles`, `excludedXHandles`, `fromDate`, `toDate`) to limit which posts are fetched:

```php
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\CompletionOptions;
use MichaelFrank\OpenRouter\Requests\Messages\UserMessage;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\XSearchFilter;
use MichaelFrank\OpenRouter\Requests\Tools\OpenRouterWebSearchTool;

// With search filters:
$request = new CompletionRequest(
    model: '~x-ai/grok-latest',
    messages: [new UserMessage('What did OpenRouter announce this year?')],
    options: new CompletionOptions(
        tools: [
            new OpenRouterWebSearchTool(
                xSearch: new XSearchFilter(
                    allowedXHandles: ['OpenRouterAI'],
                    fromDate: '2026-01-01',
                    toDate: '2026-06-01',
                    enableImageUnderstanding: true,
                    enableVideoUnderstanding: false
                )
            )
        ]
    )
);

$response = $client->completions($request);

// Enable X Search with no filters:
$options = new CompletionOptions(
    tools: [
        new OpenRouterWebSearchTool(
            xSearch: true // or: XSearchFilter::enabled()
        )
    ]
);
```

On the Responses API (or completions), you can also pass SpaceXAI's native tool directly:

```php
use MichaelFrank\OpenRouter\Requests\Tools\SpaceXXSearchTool;

// Direct SpaceXAI { "type": "x_search" } tool:
$options = new CompletionOptions(
    tools: [
        new SpaceXXSearchTool()
    ]
);
```

> [!NOTE]
> If you use the `web` plugin instead of server tools, the same configuration goes on the plugin: `plugins: [['id' => 'web', 'x_search' => XSearchFilter::enabled()]]`.

> [!WARNING]
> **SpaceXAI Pricing & Top-Level Filter Deprecation:**
> - **Default behavior**: Web Search requests only perform web search unless `xSearch` is explicitly configured. You are not charged for X Search if it is not enabled.
> - **Pricing**: X Search is billed at $5 per 1,000 posts fetched and $10 per 1,000 user profiles fetched (filters limit fetched posts, which limits cost).
> - **Deprecation**: The top-level `withXSearchFilter()` option is deprecated by OpenRouter in favor of configuring `xSearch` inside `OpenRouterWebSearchTool` or web plugins. Existing requests using `withXSearchFilter()` remain supported for backwards compatibility.

### Streaming Response Chunks (Guzzle Example)

To stream completions, pass a non-buffering client (configured with `'stream' => true` in Guzzle) to the factory:

```php
use GuzzleHttp\Client as GuzzleClient;
use MichaelFrank\OpenRouter\Client\OpenRouterFactory;
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\Messages\UserMessage;

// Make sure to configure Guzzle with stream => true to disable response buffering
$httpClient = new GuzzleClient(['stream' => true]);

$client = OpenRouterFactory::create(
    apiKey: $_ENV['OPENROUTER_API_KEY'],
    httpClient: $httpClient
);

$request = new CompletionRequest(
    model: 'meta-llama/llama-3-8b-instruct',
    messages: [new UserMessage('Write a poem about the Saci-Pererê, a mischievous Brazilian folklore character.')]
);

$chunks = $client->streamCompletions($request);

foreach ($chunks as $chunk) {
    echo $chunk->choices[0]->delta->content ?? '';
}
```

### Text Embeddings

```php
use MichaelFrank\OpenRouter\Requests\EmbeddingRequest;

$request = new EmbeddingRequest(
    model: 'text-embedding-3-small',
    input: 'Feijoada is one of the most traditional dishes in Brazilian cuisine.'
);

$response = $client->embeddings($request);

foreach ($response->data as $item) {
    // $item->embedding is an array of floats
    print_r($item->embedding);
}
```

### Reranking Documents

```php
use MichaelFrank\OpenRouter\Requests\RerankRequest;

$request = new RerankRequest(
    model: 'cohere/rerank-english-v3.0',
    query: 'What is the capital of Brazil?',
    documents: [
        'Brasilia is the capital of Brazil.',
        'Rio de Janeiro was the capital of Brazil until 1960 and remains a major cultural and touristic center.',
        'Sao Paulo is the largest city in Brazil by population and a major economic hub.'
    ],
    topN: 2
);

$response = $client->rerank($request);

foreach ($response->results as $result) {
    echo "Index: {$result->index}, Relevance Score: {$result->relevanceScore}\n";
}
```

### Text-to-Speech (Audio Synthesis)

```php
use MichaelFrank\OpenRouter\Requests\AudioSpeechRequest;

$request = new AudioSpeechRequest(
    model: 'openai/tts-1',
    input: 'The Iguaçu Falls roar with the power of millions of liters of water, creating one of the most breathtaking and unforgettable landscapes on the planet.',
    voice: 'alloy',
    responseFormat: 'mp3'
);

$response = $client->speech($request);

// Save the audio stream contents directly to a file
$response->saveToFile(__DIR__ . '/output.mp3');
```

### Speech-to-Text (Transcriptions)

```php
use MichaelFrank\OpenRouter\Requests\AudioTranscriptionRequest;

$request = AudioTranscriptionRequest::fromFile(
    path: __DIR__ . '/audio_file.mp3',
    model: 'hexgrad/kokoro-82m'
);

$response = $client->transcriptions($request);
echo $response->text;
```

### Image Generation

```php
use MichaelFrank\OpenRouter\Requests\ImageGenerationRequest;
use MichaelFrank\OpenRouter\Requests\ImageGenerationProviderPreferences;
use MichaelFrank\OpenRouter\Requests\ImageResolution;
use MichaelFrank\OpenRouter\Enums\ImageOutputFormat;
use MichaelFrank\OpenRouter\Enums\ImageBackground;

$request = new ImageGenerationRequest(
    model: 'krea/krea-2-medium-turbo',
    prompt: 'A sleek retro-futuristic hovercar parked on a neon-lit street in Tokyo, cyberpunk style, hyper-detailed',
    aspectRatio: '16:9',
    outputFormat: ImageOutputFormat::Png,
    resolution: new ImageResolution('512x512'), // Supports constants (e.g. ImageResolution::TWO_K) or custom strings (e.g. '1K', '512x512')
    background: ImageBackground::Auto,
    provider: new ImageGenerationProviderPreferences(
        allowFallbacks: false,
        only: ['google-ai-studio']
    )
);

// Standard image generation
$response = $client->images($request);

foreach ($response->data as $item) {
    // $item->b64Json is base64 encoded image bytes string
    $imageBytes = base64_decode($item->b64Json);
    file_put_contents(__DIR__ . '/hovercar.png', $imageBytes);
}

// Streaming image generation (SSE events)
$streamRequest = $request->withStream(true);
$chunks = $client->streamImages($streamRequest);

foreach ($chunks as $chunk) {
    // Each chunk contains one of: ImageGenPartialImageEvent, ImageGenTextChunkEvent, ImageGenCompletedEvent
    $event = $chunk->data;
    if ($event->getType() === 'image_generation.completed') {
        echo "Image generation complete!\n";
    }
}
```

### Client-Side Tool Calling (Fictional Weather Tool)

```php
use MichaelFrank\OpenRouter\Requests\CompletionOptions;
use MichaelFrank\OpenRouter\Requests\CompletionRequest;
use MichaelFrank\OpenRouter\Requests\Messages\AssistantMessage;
use MichaelFrank\OpenRouter\Requests\Messages\AssistantMessageToolCall;
use MichaelFrank\OpenRouter\Requests\Messages\ToolMessage;
use MichaelFrank\OpenRouter\Requests\Messages\UserMessage;
use MichaelFrank\OpenRouter\Requests\Tools\ToolDefinition;

// Define the tool schema
$weatherTool = new ToolDefinition(
    name: 'get_current_weather',
    description: 'Get the current weather for a specified location',
    parameters: [
        'type' => 'object',
        'properties' => [
            'location' => [
                'type' => 'string',
                'description' => 'The city to search for'
            ],
            'unit' => [
                'type' => 'string',
                'enum' => ['celsius', 'fahrenheit']
            ]
        ],
        'required' => ['location']
    ]
);

$request = new CompletionRequest(
    model: '~deepseek/deepseek-v4-flash-latest',
    messages: [new UserMessage('What is the weather like in Florianópolis?')],
    options: new CompletionOptions(tools: [$weatherTool])
);

$response = $client->completions($request);
$choice = $response->getFirstChoice();

if ($choice !== null && $choice->message->hasToolCalls()) {
    foreach ($choice->message->toolCalls as $toolCall) {
        if ($toolCall->function->name === 'get_current_weather') {
            $arguments = $toolCall->function->parseArguments();
            $city = $arguments['location'];

            // Execute local tool logic
            $weatherResult = "22°C, Sunny"; // Simulated result

            // 2. Resume conversation by presenting the tool output
            $assistantToolCall = new AssistantMessageToolCall(
                id: $toolCall->id,
                type: 'function',
                function: [
                    'name' => $toolCall->function->name,
                    'arguments' => $toolCall->function->arguments
                ]
            );

            $followUpRequest = new CompletionRequest(
                model: '~openai/gpt-mini-latest',
                messages: [
                    new UserMessage('What is the weather like in Florianópolis?'),
                    new AssistantMessage(toolCalls: [$assistantToolCall]),
                    new ToolMessage(content: $weatherResult, toolCallId: $toolCall->id)
                ]
            );

            $finalResponse = $client->completions($followUpRequest);
            echo $finalResponse->getFirstChoice()->message->content;
        }
    }
}
```

### Accessing Response Metadata & Rate Limits

```php
$response = $client->completions($request);

$metadata = $response->metadata;
echo "Request ID: " . $metadata->requestId . "\n";

// Access token consumption and cost from the response usage DTO
if ($response->usage !== null) {
    echo "Prompt Tokens: " . $response->usage->promptTokens . "\n";
    echo "Completion Tokens: " . $response->usage->completionTokens . "\n";
    echo "Request Cost: $" . ($response->usage->cost ?? '0.000000') . "\n";
}

$rateLimit = $metadata->rateLimit;
echo "Limit: " . $rateLimit->limit . "\n";
echo "Remaining: " . $rateLimit->remaining . "\n";

if ($rateLimit->resetAt !== null) {
    echo "Resets at: " . $rateLimit->resetAt->format(\DateTimeInterface::ATOM) . "\n";
    echo "Seconds remaining: " . $rateLimit->getRetryAfterSeconds() . "\n";
}
```

> [!NOTE]
> Response metadata (like `rateLimit` and `requestId`) is extracted automatically by the SDK from the HTTP response headers (`X-RateLimit-*` and `X-Generation-Id`). Because of this, `$metadata->requestId` duplicates the main `$response->id` (which is returned in the JSON body payload). The SDK captures both for consistency across all endpoint responses.


### Checking Credit Balance & Generation Stats

```php
// Check user credit details
$credits = $client->credits();
echo "Credits used: " . $credits->creditsUsed . "\n";
echo "Credits remaining: " . ($credits->creditsRemaining ?? 'Unknown') . "\n";

// Get stats for a completed generation by its ID
$response = $client->generation(id: 'some-id');
$stats = $response->data[0] ?? null;

if ($stats !== null) {
    echo "Model: " . $stats->model . "\n";
    echo "Provider: " . ($stats->provider ?? 'Unknown') . "\n";
    echo "Price: $" . $stats->price . "\n";
    echo "Standard Prompt Tokens: " . $stats->tokensPrompt . "\n";
    echo "Standard Completion Tokens: " . $stats->tokensCompletion . "\n";
    echo "Native Prompt Tokens: " . $stats->nativeTokensPrompt . "\n";
    echo "Native Completion Tokens: " . $stats->nativeTokensCompletion . "\n";
}
```

### Image Generation Models & Endpoints Discovery

You can discover available image models and their per-endpoint details (supported parameters, billing lines, passthrough capabilities):

```php
// List all available image generation models
$modelsResponse = $client->imageModels();

foreach ($modelsResponse->data as $model) {
    echo "Model ID: " . $model->id . "\n";
    echo "Display Name: " . $model->name . "\n";
    echo "Description: " . $model->description . "\n";
    echo "Input Modalities: " . implode(', ', $model->architecture->inputModalities) . "\n";
    echo "Output Modalities: " . implode(', ', $model->architecture->outputModalities) . "\n";
    
    // Check supported parameters (resolution, seed, output_compression, etc.)
    foreach ($model->supportedParameters as $param => $capability) {
        echo " - Parameter: {$param} (Type: {$capability->type})\n";
        if ($capability->type === 'enum' && $capability->values !== null) {
            echo "   Allowed values: " . implode(', ', $capability->values) . "\n";
        } elseif ($capability->type === 'range') {
            echo "   Numeric range: {$capability->min} to {$capability->max}\n";
        }
    }
}

// Retrieve detailed per-endpoint capabilities & pricing for a specific model
$endpointsResponse = $client->imageModelEndpoints(
    author: 'bytedance-seed',
    slug: 'seedream-4.5'
);

echo "Model: " . $endpointsResponse->id . "\n";
foreach ($endpointsResponse->endpoints as $endpoint) {
    echo "Provider: " . $endpoint->providerName . " ({$endpoint->providerSlug})\n";
    echo "Supports streaming: " . ($endpoint->supportsStreaming ? 'Yes' : 'No') . "\n";
    
    // Detailed pricing entries per dimension
    foreach ($endpoint->pricing as $price) {
        echo " - Pricing: {$price->billable} cost: \${$price->costUsd} per {$price->unit}\n";
    }
}
```

---

## Error Handling

All custom exceptions thrown by the SDK implement the `MichaelFrank\OpenRouter\Exceptions\OpenRouterException` interface. You can catch specific exceptions to handle different failure modes:

| Exception | Description | Helper Methods |
|---|---|---|
| `ValidationException` | Thrown if client inputs or request configurations fail validation. | None |
| `ConfigurationException` | Thrown for configuration issues (e.g. non-HTTPS URI in production). | None |
| `NetworkException` | Thrown when a PSR-18 network/communication error occurs. | `getPrevious()` (returns underlying PSR-18 client exception) |
| `ApiRequestException` | Thrown when the OpenRouter server returns an HTTP status code `>= 400`. | `getStatusCode()`, `getResponseBody()`, `getMetadata()` |
| `ApiResponseException` | Thrown when the response payload is malformed or contains an in-body error. | `getResponseBody()`, `getMetadata()` |

### Handling Errors & Extracting Metadata Example:

```php
use MichaelFrank\OpenRouter\Exceptions\OpenRouterException;
use MichaelFrank\OpenRouter\Exceptions\ApiRequestException;
use MichaelFrank\OpenRouter\Exceptions\NetworkException;

try {
    $response = $client->completions($request);
} catch (ApiRequestException $e) {
    // Inspect HTTP status code (e.g., 429, 401, 500)
    echo "API Error Status: " . $e->getStatusCode() . "\n";
    echo "Error Details: " . $e->getMessage() . "\n";
    echo "Raw Response Body: " . $e->getResponseBody() . "\n";

    // Extract rate limits or generation headers if present
    if ($e->getMetadata() !== null) {
        $rateLimit = $e->getMetadata()->rateLimit;
        echo "Remaining requests: " . $rateLimit->remaining . "\n";
        if ($rateLimit->resetAt !== null) {
            echo "Retry after: " . $rateLimit->getRetryAfterSeconds() . " seconds\n";
        }
    }
} catch (NetworkException $e) {
    // Handle transient network issues
    echo "Network communication failure: " . $e->getMessage() . "\n";
} catch (OpenRouterException $e) {
    // Fallback for general SDK errors (validation, config, etc.)
    echo "SDK Exception: " . $e->getMessage() . "\n";
}
```

---

## Running Quality Pipeline Locally

```bash
composer check
```

Or individual checks:

```bash
composer cs      # Code style validator (PSR-12)
composer stan    # Static analysis (PHPStan Level 9)
composer psalm   # Strict type checker (Psalm Level 1)
composer test    # PHPUnit test runner
```
