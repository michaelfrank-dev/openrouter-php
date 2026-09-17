<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\PercentileCutoffs;

final class PercentileCutoffsTest extends TestCase
{
    public function testToArrayAndSerialization(): void
    {
        $cutoffs = new PercentileCutoffs(
            p50: 0.5,
            p99: 2.0
        );

        $expected = [
            'p50' => 0.5,
            'p99' => 2.0,
        ];

        $this->assertEquals($expected, $cutoffs->toArray());
        $this->assertEquals($expected, $cutoffs->jsonSerialize());
    }
}
