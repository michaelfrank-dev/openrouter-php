<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Exceptions\ValidationException;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\XSearchFilter;
use PHPUnit\Framework\TestCase;

final class XSearchFilterTest extends TestCase
{
    public function testSerialization(): void
    {
        $filter = new XSearchFilter(
            allowedXHandles: ['OpenRouterAI', 'xai'],
            fromDate: '2025-01-01',
            toDate: '2025-12-31',
            enableImageUnderstanding: true
        );

        $array = $filter->toArray();

        $this->assertEquals([
            'allowed_x_handles' => ['OpenRouterAI', 'xai'],
            'from_date' => '2025-01-01',
            'to_date' => '2025-12-31',
            'enable_image_understanding' => true
        ], $array);
        $this->assertEquals($array, $filter->jsonSerialize());
    }

    public function testMutuallyExclusiveHandlesThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('allowed_x_handles and excluded_x_handles are mutually exclusive.');

        new XSearchFilter(
            allowedXHandles: ['handle1'],
            excludedXHandles: ['handle2']
        );
    }

    public function testTooManyAllowedHandlesThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('allowed_x_handles cannot exceed 10 items.');

        new XSearchFilter(
            allowedXHandles: array_fill(0, 11, 'handle')
        );
    }

    public function testTooManyExcludedHandlesThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('excluded_x_handles cannot exceed 10 items.');

        new XSearchFilter(
            excludedXHandles: array_fill(0, 11, 'handle')
        );
    }

    public function testEmptySerializationSerializesToObject(): void
    {
        $filter = new XSearchFilter();

        $this->assertSame([], $filter->toArray());
        $serialized = $filter->jsonSerialize();
        $this->assertInstanceOf(\stdClass::class, $serialized);
        $this->assertSame('{}', json_encode($filter));
    }

    public function testEnabledHelperReturnsEmptyInstance(): void
    {
        $filter = XSearchFilter::enabled();

        $this->assertInstanceOf(XSearchFilter::class, $filter);
        $this->assertSame([], $filter->toArray());
        $this->assertSame('{}', json_encode($filter));
    }
}
