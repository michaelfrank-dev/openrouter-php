<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests\ProviderRouting;

/**
 * Class Quantization
 *
 * String-tolerant wrapper for provider quantization values.
 *
 * @package MichaelFrank\OpenRouter\Requests\ProviderRouting
 */
final readonly class Quantization
{
    public const INT4 = 'int4';
    public const INT8 = 'int8';
    public const FP4 = 'fp4';
    public const FP6 = 'fp6';
    public const FP8 = 'fp8';
    public const FP16 = 'fp16';
    public const BF16 = 'bf16';
    public const FP32 = 'fp32';
    public const UNKNOWN = 'unknown';

    /**
     * Quantization constructor.
     *
     * @param string $value
     */
    public function __construct(public string $value)
    {
    }

    /**
     * Creates an instance of Quantization from a raw string.
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
