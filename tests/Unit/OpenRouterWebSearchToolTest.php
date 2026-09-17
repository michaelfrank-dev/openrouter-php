<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\SearchContextSize;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\WebSearchEngine;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\XSearchFilter;
use MichaelFrank\OpenRouter\Requests\Tools\OpenRouterWebSearchTool;
use PHPUnit\Framework\TestCase;

/**
 * Class OpenRouterWebSearchToolTest
 *
 * Tests serialization and validation for OpenRouterWebSearchTool.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class OpenRouterWebSearchToolTest extends TestCase
{
    public function testSerializationWithParams(): void
    {
        $tool = new OpenRouterWebSearchTool(
            engine: new WebSearchEngine(WebSearchEngine::EXA),
            maxResults: 5,
            maxTotalResults: 15,
            searchContextSize: new SearchContextSize(SearchContextSize::MEDIUM),
            maxCharacters: 2000,
            allowedDomains: ['wikipedia.org'],
            excludedDomains: ['pinterest.com'],
            maxUses: 3
        );

        $array = $tool->toArray();

        $this->assertEquals('openrouter:web_search', $array['type']);
        $this->assertArrayHasKey('parameters', $array);

        $params = $array['parameters'] ?? [];
        $this->assertEquals('exa', $params['engine'] ?? null);
        $this->assertEquals(5, $params['max_results'] ?? null);
        $this->assertEquals(15, $params['max_total_results'] ?? null);
        $this->assertEquals('medium', $params['search_context_size'] ?? null);
        $this->assertEquals(2000, $params['max_characters'] ?? null);
        $this->assertEquals(['wikipedia.org'], $params['allowed_domains'] ?? null);
        $this->assertEquals(['pinterest.com'], $params['excluded_domains'] ?? null);
        $this->assertEquals(3, $params['max_uses'] ?? null);
        $this->assertEquals($array, $tool->jsonSerialize());
    }

    public function testSerializationEmpty(): void
    {
        $tool = new OpenRouterWebSearchTool();
        $array = $tool->toArray();

        $this->assertEquals('openrouter:web_search', $array['type']);
        $this->assertArrayNotHasKey('parameters', $array);
    }

    public function testSerializationWithXSearchBoolTrue(): void
    {
        $tool = new OpenRouterWebSearchTool(xSearch: true);
        $array = $tool->toArray();

        $this->assertEquals('openrouter:web_search', $array['type']);
        $this->assertArrayHasKey('parameters', $array);
        $params = $array['parameters'] ?? [];
        $this->assertInstanceOf(\stdClass::class, $params['x_search'] ?? null);

        $json = json_encode($tool);
        $this->assertSame('{"type":"openrouter:web_search","parameters":{"x_search":{}}}', $json);
    }

    public function testSerializationWithXSearchBoolFalse(): void
    {
        $tool = new OpenRouterWebSearchTool(xSearch: false);
        $array = $tool->toArray();

        $this->assertArrayNotHasKey('parameters', $array);
    }

    public function testSerializationWithXSearchEmptyFilter(): void
    {
        $tool = new OpenRouterWebSearchTool(xSearch: new XSearchFilter());
        $array = $tool->toArray();

        $this->assertArrayHasKey('parameters', $array);
        $params = $array['parameters'] ?? [];
        $this->assertInstanceOf(\stdClass::class, $params['x_search'] ?? null);

        $json = json_encode($tool);
        $this->assertSame('{"type":"openrouter:web_search","parameters":{"x_search":{}}}', $json);
    }

    public function testSerializationWithXSearchFilterParams(): void
    {
        $tool = new OpenRouterWebSearchTool(
            xSearch: new XSearchFilter(
                allowedXHandles: ['OpenRouterAI'],
                fromDate: '2026-01-01'
            )
        );
        $array = $tool->toArray();

        $this->assertArrayHasKey('parameters', $array);
        $params = $array['parameters'] ?? [];
        $this->assertSame([
            'allowed_x_handles' => ['OpenRouterAI'],
            'from_date' => '2026-01-01',
        ], $params['x_search'] ?? null);

        $json = json_encode($tool);
        $this->assertSame(
            '{"type":"openrouter:web_search","parameters":{"x_search":{"allowed_x_handles":["OpenRouterAI"],"from_date":"2026-01-01"}}}',
            $json
        );
    }

    /**
     * @dataProvider mutuallyExclusiveEnginesProvider
     */
    public function testMutuallyExclusiveDomainsThrowsException(string $engineValue): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf(
            'allowedDomains and excludedDomains are mutually exclusive for engine "%s".',
            $engineValue
        ));

        new OpenRouterWebSearchTool(
            engine: new WebSearchEngine($engineValue),
            allowedDomains: ['wikipedia.org'],
            excludedDomains: ['pinterest.com']
        );
    }

    /**
     * @return array<string, array{string}>
     */
    public static function mutuallyExclusiveEnginesProvider(): array
    {
        return [
            'parallel' => ['parallel'],
            'firecrawl' => ['firecrawl'],
            'perplexity' => ['perplexity'],
        ];
    }

    /**
     * @dataProvider compatibleEnginesProvider
     */
    public function testCompatibleEnginesDoNotThrowException(string $engineValue): void
    {
        $tool = new OpenRouterWebSearchTool(
            engine: new WebSearchEngine($engineValue),
            allowedDomains: ['wikipedia.org'],
            excludedDomains: ['pinterest.com']
        );

        $this->assertInstanceOf(OpenRouterWebSearchTool::class, $tool);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function compatibleEnginesProvider(): array
    {
        return [
            'exa' => ['exa'],
            'auto' => ['auto'],
            'native' => ['native'],
        ];
    }
}
