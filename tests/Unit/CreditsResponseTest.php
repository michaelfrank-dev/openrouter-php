<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Tests\Unit;

use MichaelFrank\OpenRouter\Responses\CreditsResponse;
use MichaelFrank\OpenRouter\Metadata\ResponseMetadata;
use MichaelFrank\OpenRouter\Metadata\RateLimit;
use PHPUnit\Framework\TestCase;

/**
 * Class CreditsResponseTest
 *
 * Tests the mapping, parsing, and fallback calculations in the CreditsResponse class.
 *
 * @package MichaelFrank\OpenRouter\Tests\Unit
 */
final class CreditsResponseTest extends TestCase
{
    /**
     * Test constructor sets fields correctly.
     *
     * @return void
     */
    public function testConstructorSetsProperties(): void
    {
        $metadata = new ResponseMetadata('req-123', new RateLimit(100, 99, new \DateTimeImmutable('+10 seconds')));
        $credits = new CreditsResponse(100.0, 25.0, 75.0, $metadata);

        $this->assertEquals(100.0, $credits->creditsPurchased);
        $this->assertEquals(25.0, $credits->creditsUsed);
        $this->assertEquals(75.0, $credits->creditsRemaining);
        $this->assertSame($metadata, $credits->metadata);
    }

    /**
     * Test mapping of standard OpenRouter API structure.
     *
     * @return void
     */
    public function testFromStandardOpenRouterPayload(): void
    {
        $payload = [
            'data' => [
                'total_credits' => 100.5,
                'total_usage' => 25.75,
            ]
        ];

        $credits = CreditsResponse::fromArray($payload);

        $this->assertEquals(100.5, $credits->creditsPurchased);
        $this->assertEquals(25.75, $credits->creditsUsed);
        // Fallback calculation: 100.5 - 25.75 = 74.75
        $this->assertEquals(74.75, $credits->creditsRemaining);
    }

    /**
     * Test that explicit remaining field takes priority over fallback calculation.
     *
     * @return void
     */
    public function testExplicitRemainingKeyTakesPriority(): void
    {
        $payload = [
            'data' => [
                'total_credits' => 100.0,
                'total_usage' => 25.0,
                'credits_remaining' => 80.0, // Differing remaining value to verify priority
            ]
        ];

        $credits = CreditsResponse::fromArray($payload);

        $this->assertEquals(100.0, $credits->creditsPurchased);
        $this->assertEquals(25.0, $credits->creditsUsed);
        $this->assertEquals(80.0, $credits->creditsRemaining);
    }

    /**
     * Test fallback calculation when other alternative keys are used (e.g. limit and total_usage).
     *
     * @return void
     */
    public function testFallbackCalculationWithAlternativeKeys(): void
    {
        $payload = [
            'limit' => 50.0,
            'usage' => 10.0,
        ];

        $credits = CreditsResponse::fromArray($payload);

        $this->assertEquals(50.0, $credits->creditsPurchased);
        $this->assertEquals(10.0, $credits->creditsUsed);
        $this->assertEquals(40.0, $credits->creditsRemaining);
    }

    /**
     * Test behavior with nulls and missing values.
     *
     * @return void
     */
    public function testBehaviorWithNulls(): void
    {
        $payload = [
            'data' => [
                'total_credits' => null,
                'total_usage' => null,
            ]
        ];

        $credits = CreditsResponse::fromArray($payload);

        $this->assertNull($credits->creditsPurchased);
        $this->assertNull($credits->creditsUsed);
        $this->assertNull($credits->creditsRemaining);
    }
}
