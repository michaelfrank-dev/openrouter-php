<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MichaelFrank\OpenRouter\Requests\ProviderRouting\UserLocation;

final class UserLocationTest extends TestCase
{
    public function testToArrayAndSerialization(): void
    {
        $location = new UserLocation(
            city: 'San Francisco',
            country: 'US'
        );

        $expected = [
            'type' => 'approximate',
            'city' => 'San Francisco',
            'country' => 'US',
        ];

        $this->assertEquals($expected, $location->toArray());
        $this->assertEquals($expected, $location->jsonSerialize());
    }
}
