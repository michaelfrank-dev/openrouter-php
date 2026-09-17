<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Requests\ProviderRouting\SearchContextSize;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\WebSearchOptions;
use PHPUnit\Framework\TestCase;

final class WebSearchOptionsTest extends TestCase
{
    public function testSerialization(): void
    {
        $options = new WebSearchOptions(new SearchContextSize(SearchContextSize::HIGH));
        $array = $options->toArray();

        $this->assertEquals(['search_context_size' => 'high'], $array);
        $this->assertEquals($array, $options->jsonSerialize());
    }

    public function testSerializationWithNull(): void
    {
        $options = new WebSearchOptions(null);
        $this->assertEquals([], $options->toArray());
    }
}
