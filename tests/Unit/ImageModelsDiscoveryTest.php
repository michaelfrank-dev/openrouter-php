<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Responses\CapabilityDescriptor;
use MichaelFrank\OpenRouter\Responses\ImageEndpoint;
use MichaelFrank\OpenRouter\Responses\ImageModelArchitecture;
use MichaelFrank\OpenRouter\Responses\ImageModelEndpointsResponse;
use MichaelFrank\OpenRouter\Responses\ImageModelListItem;
use MichaelFrank\OpenRouter\Responses\ImageModelsListResponse;
use MichaelFrank\OpenRouter\Responses\ImagePricingEntry;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageModelsDiscoveryTest
 *
 * Unit tests covering Response DTO parsing and instantiation for Image Models Discovery features.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class ImageModelsDiscoveryTest extends TestCase
{
    public function testCapabilityDescriptorParsesCorrectly(): void
    {
        // Boolean Capability
        $boolDesc = CapabilityDescriptor::fromArray(['type' => 'boolean']);
        $this->assertEquals('boolean', $boolDesc->type);
        $this->assertNull($boolDesc->values);
        $this->assertNull($boolDesc->min);
        $this->assertNull($boolDesc->max);

        // Enum Capability
        $enumDesc = CapabilityDescriptor::fromArray([
            'type' => 'enum',
            'values' => ['1K', '2K', '4K']
        ]);
        $this->assertEquals('enum', $enumDesc->type);
        $this->assertEquals(['1K', '2K', '4K'], $enumDesc->values);
        $this->assertNull($enumDesc->min);
        $this->assertNull($enumDesc->max);

        // Range Capability
        $rangeDesc = CapabilityDescriptor::fromArray([
            'type' => 'range',
            'min' => 0,
            'max' => 100
        ]);
        $this->assertEquals('range', $rangeDesc->type);
        $this->assertNull($rangeDesc->values);
        $this->assertEquals(0.0, $rangeDesc->min);
        $this->assertEquals(100.0, $rangeDesc->max);
    }

    public function testImageModelArchitectureParsesCorrectly(): void
    {
        $arch = ImageModelArchitecture::fromArray([
            'input_modalities' => ['text', 'image'],
            'output_modalities' => ['image']
        ]);

        $this->assertEquals(['text', 'image'], $arch->inputModalities);
        $this->assertEquals(['image'], $arch->outputModalities);
    }

    public function testImageModelListItemParsesCorrectly(): void
    {
        $payload = [
            'id' => 'bytedance-seed/seedream-4.5',
            'name' => 'Seedream 4.5',
            'description' => 'A text-to-image model.',
            'created' => 1692901234,
            'architecture' => [
                'input_modalities' => ['text'],
                'output_modalities' => ['image']
            ],
            'supported_parameters' => [
                'resolution' => [
                    'type' => 'enum',
                    'values' => ['1K', '2K']
                ]
            ],
            'supports_streaming' => false,
            'endpoints' => '/api/v1/images/models/bytedance-seed/seedream-4.5/endpoints'
        ];

        $item = ImageModelListItem::fromArray($payload);

        $this->assertEquals('bytedance-seed/seedream-4.5', $item->id);
        $this->assertEquals('Seedream 4.5', $item->name);
        $this->assertEquals('A text-to-image model.', $item->description);
        $this->assertEquals(1692901234, $item->created);
        $this->assertEquals(['text'], $item->architecture->inputModalities);
        $this->assertEquals(['image'], $item->architecture->outputModalities);
        $this->assertArrayHasKey('resolution', $item->supportedParameters);
        $this->assertEquals('enum', $item->supportedParameters['resolution']->type);
        $this->assertEquals(['1K', '2K'], $item->supportedParameters['resolution']->values);
        $this->assertFalse($item->supportsStreaming);
        $this->assertEquals('/api/v1/images/models/bytedance-seed/seedream-4.5/endpoints', $item->endpoints);
    }

    public function testImageModelsListResponseParsesCorrectly(): void
    {
        $payload = [
            'data' => [
                [
                    'id' => 'bytedance-seed/seedream-4.5',
                    'name' => 'Seedream 4.5',
                    'description' => 'A text-to-image model.',
                    'created' => 1692901234,
                    'architecture' => [
                        'input_modalities' => ['text'],
                        'output_modalities' => ['image']
                    ],
                    'supported_parameters' => [
                        'resolution' => [
                            'type' => 'enum',
                            'values' => ['1K']
                        ]
                    ],
                    'supports_streaming' => false,
                    'endpoints' => '/api/v1/images/models/bytedance-seed/seedream-4.5/endpoints'
                ]
            ]
        ];

        $response = ImageModelsListResponse::fromArray($payload);

        $this->assertCount(1, $response->data);
        $this->assertEquals('bytedance-seed/seedream-4.5', $response->data[0]->id);
    }

    public function testImagePricingEntryParsesCorrectly(): void
    {
        $payload = [
            'billable' => 'output_image',
            'cost_usd' => 0.05,
            'unit' => 'image',
            'variant' => 'hd'
        ];

        $pricing = ImagePricingEntry::fromArray($payload);

        $this->assertEquals('output_image', $pricing->billable);
        $this->assertEquals(0.05, $pricing->costUsd);
        $this->assertEquals('image', $pricing->unit);
        $this->assertEquals('hd', $pricing->variant);
    }

    public function testImageEndpointParsesCorrectly(): void
    {
        $payload = [
            'allowed_passthrough_parameters' => ['aspect_ratio'],
            'pricing' => [
                [
                    'billable' => 'output_image',
                    'cost_usd' => 0.05,
                    'unit' => 'image'
                ]
            ],
            'provider_name' => 'Bytedance',
            'provider_slug' => 'bytedance',
            'provider_tag' => 'bytedance',
            'supported_parameters' => [
                'resolution' => [
                    'type' => 'enum',
                    'values' => ['1K']
                ]
            ],
            'supports_streaming' => false
        ];

        $endpoint = ImageEndpoint::fromArray($payload);

        $this->assertEquals(['aspect_ratio'], $endpoint->allowedPassthroughParameters);
        $this->assertCount(1, $endpoint->pricing);
        $this->assertEquals('output_image', $endpoint->pricing[0]->billable);
        $this->assertEquals('Bytedance', $endpoint->providerName);
        $this->assertEquals('bytedance', $endpoint->providerSlug);
        $this->assertEquals('bytedance', $endpoint->providerTag);
        $this->assertArrayHasKey('resolution', $endpoint->supportedParameters);
        $this->assertFalse($endpoint->supportsStreaming);
    }

    public function testImageModelEndpointsResponseParsesCorrectly(): void
    {
        $payload = [
            'id' => 'bytedance-seed/seedream-4.5',
            'endpoints' => [
                [
                    'allowed_passthrough_parameters' => [],
                    'pricing' => [
                        [
                            'billable' => 'output_image',
                            'cost_usd' => 0.05,
                            'unit' => 'image'
                        ]
                    ],
                    'provider_name' => 'Bytedance',
                    'provider_slug' => 'bytedance',
                    'provider_tag' => null,
                    'supported_parameters' => [],
                    'supports_streaming' => false
                ]
            ]
        ];

        $response = ImageModelEndpointsResponse::fromArray($payload);

        $this->assertEquals('bytedance-seed/seedream-4.5', $response->id);
        $this->assertCount(1, $response->endpoints);
        $this->assertEquals('Bytedance', $response->endpoints[0]->providerName);
        $this->assertNull($response->endpoints[0]->providerTag);
    }
}
