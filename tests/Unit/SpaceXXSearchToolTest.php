<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Requests\ProviderRouting\XSearchFilter;
use MichaelFrank\OpenRouter\Requests\Tools\SpaceXXSearchTool;
use PHPUnit\Framework\TestCase;

/**
 * Class SpaceXXSearchToolTest
 *
 * Tests serialization for SpaceXXSearchTool.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class SpaceXXSearchToolTest extends TestCase
{
    public function testSerializationWithoutFilters(): void
    {
        $tool = new SpaceXXSearchTool();
        $array = $tool->toArray();

        $this->assertSame('x_search', $tool->getType());
        $this->assertSame(['type' => 'x_search'], $array);
        $this->assertSame($array, $tool->jsonSerialize());
        $this->assertSame('{"type":"x_search"}', json_encode($tool));
    }

    public function testSerializationWithEmptyFilters(): void
    {
        $tool = new SpaceXXSearchTool(new XSearchFilter());
        $array = $tool->toArray();

        $this->assertSame(['type' => 'x_search'], $array);
        $this->assertSame('{"type":"x_search"}', json_encode($tool));
    }

    public function testSerializationWithFilters(): void
    {
        $filters = new XSearchFilter(
            allowedXHandles: ['OpenRouterAI'],
            fromDate: '2026-01-01'
        );
        $tool = new SpaceXXSearchTool($filters);
        $array = $tool->toArray();

        $this->assertSame('x_search', $array['type']);
        $this->assertArrayHasKey('parameters', $array);
        $params = $array['parameters'] ?? [];
        $this->assertSame([
            'allowed_x_handles' => ['OpenRouterAI'],
            'from_date' => '2026-01-01',
        ], $params);

        $json = json_encode($tool);
        $this->assertSame(
            '{"type":"x_search","parameters":{"allowed_x_handles":["OpenRouterAI"],"from_date":"2026-01-01"}}',
            $json
        );
    }
}
