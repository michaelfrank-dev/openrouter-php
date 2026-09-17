<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MichaelFrank\OpenRouter\Requests\ProviderPreferences;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\ProviderSort;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\PercentileCutoffs;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\Quantization;

final class ProviderPreferencesTest extends TestCase
{
    public function testFluentBuilders(): void
    {
        $prefs = new ProviderPreferences();

        // withOrder
        $p1 = $prefs->withOrder(['openai']);
        $this->assertNotSame($prefs, $p1);
        $this->assertEquals(['openai'], $p1->order);

        // withSort
        $sort = new ProviderSort(ProviderSort::THROUGHPUT);
        $p2 = $p1->withSort($sort);
        $this->assertNotSame($p1, $p2);
        $this->assertSame($sort, $p2->sort);
        $this->assertEquals(['openai'], $p2->order);

        // withAllowFallbacks
        $p3 = $p2->withAllowFallbacks(true);
        $this->assertNotSame($p2, $p3);
        $this->assertTrue($p3->allowFallbacks);

        // withRequireParameters
        $p4 = $p3->withRequireParameters(true);
        $this->assertNotSame($p3, $p4);
        $this->assertTrue($p4->requireParameters);

        // withEnforceDistillableText
        $p5 = $p4->withEnforceDistillableText(true);
        $this->assertNotSame($p4, $p5);
        $this->assertTrue($p5->enforceDistillableText);

        // withZdr
        $p6 = $p5->withZdr(true);
        $this->assertNotSame($p5, $p6);
        $this->assertTrue($p6->zdr);

        // withDataCollection
        $p7 = $p6->withDataCollection('deny');
        $this->assertNotSame($p6, $p7);
        $this->assertEquals('deny', $p7->dataCollection);

        // withOnly
        $p8 = $p7->withOnly(['google']);
        $this->assertNotSame($p7, $p8);
        $this->assertEquals(['google'], $p8->only);

        // withIgnore
        $p9 = $p8->withIgnore(['anthropic']);
        $this->assertNotSame($p8, $p9);
        $this->assertEquals(['anthropic'], $p9->ignore);

        // withPreferredMinThroughput
        $cutoffs = new PercentileCutoffs(p50: 10.0);
        $p10 = $p9->withPreferredMinThroughput($cutoffs);
        $this->assertNotSame($p9, $p10);
        $this->assertSame($cutoffs, $p10->preferredMinThroughput);

        // withPreferredMaxLatency
        $p11 = $p10->withPreferredMaxLatency(2.5);
        $this->assertNotSame($p10, $p11);
        $this->assertEquals(2.5, $p11->preferredMaxLatency);

        // withMaxPrice
        $p12 = $p11->withMaxPrice(['prompt' => '0.01']);
        $this->assertNotSame($p11, $p12);
        $this->assertEquals(['prompt' => '0.01'], $p12->maxPrice);

        // withRequireFeature
        $p13 = $p12->withRequireFeature(['tools']);
        $this->assertNotSame($p12, $p13);
        $this->assertEquals(['tools'], $p13->requireFeature);

        // withQuantizations
        $quant = [new Quantization(Quantization::INT4)];
        $p14 = $p13->withQuantizations($quant);
        $this->assertNotSame($p13, $p14);
        $this->assertSame($quant, $p14->quantizations);
    }
}
