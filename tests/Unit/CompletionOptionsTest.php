<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MichaelFrank\OpenRouter\Requests\CompletionOptions;
use MichaelFrank\OpenRouter\Requests\ProviderPreferences;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\ProviderSort;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\SearchContextSize;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\WebSearchOptions;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\XSearchFilter;
use MichaelFrank\OpenRouter\Requests\Tools\ToolDefinition;
use MichaelFrank\OpenRouter\Requests\Tools\OpenRouterWebSearchTool;
use MichaelFrank\OpenRouter\Requests\Tools\OpenRouterWebFetchTool;

final class CompletionOptionsTest extends TestCase
{
    public function testImmutability(): void
    {
        $options = new CompletionOptions(temperature: 0.7);
        $newOptions = $options->withStream(true);

        $this->assertNotSame($options, $newOptions);
        $this->assertFalse($options->stream);
        $this->assertTrue($newOptions->stream);
        $this->assertEquals(0.7, $newOptions->temperature);

        $provider = new ProviderPreferences(allowFallbacks: true);
        $prefsOptions = $newOptions->withProviderPreferences($provider);

        $this->assertNotSame($newOptions, $prefsOptions);
        $this->assertSame($provider, $prefsOptions->provider);
    }

    public function testSerializationRules(): void
    {
        $options = new CompletionOptions(
            temperature: 0.5,
            stream: true,
            tools: [
                new ToolDefinition('my_func', 'Desc', ['type' => 'object']),
                new OpenRouterWebSearchTool(maxResults: 10)
            ],
            provider: new ProviderPreferences(
                order: ['openai'],
                sort: new ProviderSort(ProviderSort::PRICE),
                allowFallbacks: null // Should be omitted since null
            )
        );

        $array = $options->toArray();

        $this->assertEquals(0.5, $array['temperature']);
        $this->assertTrue($array['stream']);

        $tools = $array['tools'] ?? [];
        $this->assertIsArray($tools);
        $this->assertCount(2, $tools);

        $tool0 = $tools[0] ?? null;
        $this->assertIsArray($tool0);
        $this->assertEquals('function', $tool0['type'] ?? null);
        $this->assertArrayHasKey('function', $tool0);
        $toolFunction = $tool0['function'] ?? null;
        $this->assertIsArray($toolFunction);
        $this->assertEquals('my_func', $toolFunction['name'] ?? null);

        $tool1 = $tools[1] ?? null;
        $this->assertIsArray($tool1);
        $this->assertEquals('openrouter:web_search', $tool1['type'] ?? null);
        $this->assertArrayHasKey('parameters', $tool1);
        $toolParams = $tool1['parameters'] ?? null;
        $this->assertIsArray($toolParams);
        $this->assertEquals(10, $toolParams['max_results'] ?? null);

        $provider = $array['provider'] ?? [];
        $this->assertIsArray($provider);
        /** @var array<string, mixed> $provider */

        $this->assertEquals(['openai'], $provider['order']);
        $this->assertEquals('price', $provider['sort']);
        $this->assertArrayNotHasKey('allow_fallbacks', $provider);
    }

    public function testWebSearchAndXSearchIntegration(): void
    {
        $webSearchOptions = new WebSearchOptions(new SearchContextSize(SearchContextSize::HIGH));
        $xSearchFilter = new XSearchFilter(allowedXHandles: ['OpenRouterAI']);

        $options = new CompletionOptions(
            webSearchOptions: $webSearchOptions,
            xSearchFilter: $xSearchFilter,
            plugins: [
                ['id' => 'web', 'engine' => 'exa', 'max_results' => 3]
            ]
        );

        $array = $options->toArray();

        $this->assertEquals(['search_context_size' => 'high'], $array['web_search_options']);
        $this->assertEquals(['allowed_x_handles' => ['OpenRouterAI']], $array['x_search_filter']);
        $this->assertEquals([['id' => 'web', 'engine' => 'exa', 'max_results' => 3]], $array['plugins']);

        // Test with helpers
        $updatedOptions = (new CompletionOptions())
            ->withWebSearchOptions($webSearchOptions)
            ->withXSearchFilter($xSearchFilter);

        $this->assertSame($webSearchOptions, $updatedOptions->webSearchOptions);
        $this->assertSame($xSearchFilter, $updatedOptions->xSearchFilter);

        $plugins = [['id' => 'web', 'engine' => 'exa']];
        $updatedWithPlugins = $updatedOptions->withPlugins($plugins);
        $this->assertNotSame($updatedOptions, $updatedWithPlugins);
        $this->assertEquals($plugins, $updatedWithPlugins->plugins);

        // Test fromArray parsing
        $fromArray = CompletionOptions::fromArray([
            'web_search_options' => [
                'search_context_size' => 'high'
            ],
            'x_search_filter' => [
                'allowed_x_handles' => ['OpenRouterAI']
            ],
            'plugins' => [
                ['id' => 'web', 'engine' => 'exa']
            ]
        ]);

        $this->assertInstanceOf(WebSearchOptions::class, $fromArray->webSearchOptions);
        $this->assertNotNull($fromArray->webSearchOptions->searchContextSize);
        $this->assertEquals(SearchContextSize::HIGH, $fromArray->webSearchOptions->searchContextSize->value);
        $this->assertInstanceOf(XSearchFilter::class, $fromArray->xSearchFilter);
        $this->assertEquals(['OpenRouterAI'], $fromArray->xSearchFilter->allowedXHandles);
        $this->assertEquals([['id' => 'web', 'engine' => 'exa']], $fromArray->plugins);
    }

    public function testWebToolsSerialization(): void
    {
        $searchWithParams = new OpenRouterWebSearchTool(maxResults: 5, maxUses: 3);
        $searchEmpty = new OpenRouterWebSearchTool();

        $fetchWithParams = new OpenRouterWebFetchTool(maxContentTokens: 1000, maxUses: 2);
        $fetchEmpty = new OpenRouterWebFetchTool();

        $arraySearchWithParams = $searchWithParams->toArray();
        $this->assertEquals('openrouter:web_search', $arraySearchWithParams['type']);
        $this->assertArrayHasKey('parameters', $arraySearchWithParams);
        $searchParams = $arraySearchWithParams['parameters'] ?? [];
        $this->assertEquals(5, $searchParams['max_results']);
        $this->assertEquals(3, $searchParams['max_uses']);

        $arraySearchEmpty = $searchEmpty->toArray();
        $this->assertEquals('openrouter:web_search', $arraySearchEmpty['type']);
        $this->assertArrayNotHasKey('parameters', $arraySearchEmpty);

        $arrayFetchWithParams = $fetchWithParams->toArray();
        $this->assertEquals('openrouter:web_fetch', $arrayFetchWithParams['type']);
        $this->assertArrayHasKey('parameters', $arrayFetchWithParams);
        $fetchParams = $arrayFetchWithParams['parameters'] ?? [];
        $this->assertEquals(1000, $fetchParams['max_content_tokens']);
        $this->assertEquals(2, $fetchParams['max_uses']);

        $arrayFetchEmpty = $fetchEmpty->toArray();
        $this->assertEquals('openrouter:web_fetch', $arrayFetchEmpty['type']);
        $this->assertArrayNotHasKey('parameters', $arrayFetchEmpty);
    }

    public function testWebPluginWithXSearchAndWebSearchToolWithXSearch(): void
    {
        $options = new CompletionOptions(
            tools: [
                new OpenRouterWebSearchTool(
                    xSearch: new XSearchFilter(allowedXHandles: ['OpenRouterAI'])
                ),
            ],
            plugins: [
                ['id' => 'web', 'x_search' => new XSearchFilter()]
            ]
        );

        $json = json_encode($options->toArray());
        $this->assertIsString($json);
        $this->assertStringContainsString('"x_search":{"allowed_x_handles":["OpenRouterAI"]}', $json);
        $this->assertStringContainsString('"plugins":[{"id":"web","x_search":{}}]', $json);
    }
}
