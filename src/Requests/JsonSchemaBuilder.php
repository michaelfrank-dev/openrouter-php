<?php

declare(strict_types=1);

namespace MichaelFrank\OpenRouter\Requests;

/**
 * Class JsonSchemaBuilder
 *
 * Fluent builder utility to construct JSON Schemas for structured outputs.
 *
 * @package MichaelFrank\OpenRouter\Requests
 */
final class JsonSchemaBuilder
{
    /** @var array<string, array<string, mixed>> */
    private array $properties = [];

    /** @var array<string> */
    private array $required = [];

    private bool $additionalProperties = false;

    /**
     * JsonSchemaBuilder constructor.
     *
     * @param string $name
     */
    public function __construct(private readonly string $name)
    {
    }

    /**
     * Creates a new instance of the builder.
     *
     * @param string $name
     * @return self
     */
    public static function new(string $name): self
    {
        return new self($name);
    }

    /**
     * Gets the schema name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Adds a string property.
     *
     * @param string $name
     * @param string|null $description
     * @param bool $required
     * @return self
     */
    public function addString(string $name, ?string $description = null, bool $required = true): self
    {
        $prop = ['type' => 'string'];
        if ($description !== null) {
            $prop['description'] = $description;
        }
        $this->properties[$name] = $prop;
        if ($required) {
            $this->required[] = $name;
        }
        return $this;
    }

    /**
     * Adds an integer property.
     *
     * @param string $name
     * @param string|null $description
     * @param bool $required
     * @return self
     */
    public function addInteger(string $name, ?string $description = null, bool $required = true): self
    {
        $prop = ['type' => 'integer'];
        if ($description !== null) {
            $prop['description'] = $description;
        }
        $this->properties[$name] = $prop;
        if ($required) {
            $this->required[] = $name;
        }
        return $this;
    }

    /**
     * Adds a number (float) property.
     *
     * @param string $name
     * @param string|null $description
     * @param bool $required
     * @return self
     */
    public function addNumber(string $name, ?string $description = null, bool $required = true): self
    {
        $prop = ['type' => 'number'];
        if ($description !== null) {
            $prop['description'] = $description;
        }
        $this->properties[$name] = $prop;
        if ($required) {
            $this->required[] = $name;
        }
        return $this;
    }

    /**
     * Adds a boolean property.
     *
     * @param string $name
     * @param string|null $description
     * @param bool $required
     * @return self
     */
    public function addBoolean(string $name, ?string $description = null, bool $required = true): self
    {
        $prop = ['type' => 'boolean'];
        if ($description !== null) {
            $prop['description'] = $description;
        }
        $this->properties[$name] = $prop;
        if ($required) {
            $this->required[] = $name;
        }
        return $this;
    }

    /**
     * Adds an object property.
     *
     * @param string $name
     * @param array<string, mixed> $properties
     * @param array<string> $required
     * @param bool $additionalProperties
     * @param string|null $description
     * @param bool $requiredField
     * @return self
     */
    public function addObject(
        string $name,
        array $properties,
        array $required = [],
        bool $additionalProperties = false,
        ?string $description = null,
        bool $requiredField = true
    ): self {
        $prop = [
            'type' => 'object',
            'properties' => $properties,
            'required' => $required,
            'additionalProperties' => $additionalProperties,
        ];
        if ($description !== null) {
            $prop['description'] = $description;
        }
        $this->properties[$name] = $prop;
        if ($requiredField) {
            $this->required[] = $name;
        }
        return $this;
    }

    /**
     * Adds an array property.
     *
     * @param string $name
     * @param array<string, mixed> $items
     * @param string|null $description
     * @param bool $required
     * @return self
     */
    public function addArray(string $name, array $items, ?string $description = null, bool $required = true): self
    {
        $prop = [
            'type' => 'array',
            'items' => $items,
        ];
        if ($description !== null) {
            $prop['description'] = $description;
        }
        $this->properties[$name] = $prop;
        if ($required) {
            $this->required[] = $name;
        }
        return $this;
    }

    /**
     * Sets whether additional properties are allowed in the schema.
     *
     * @param bool $allow
     * @return self
     */
    public function setAdditionalProperties(bool $allow): self
    {
        $this->additionalProperties = $allow;
        return $this;
    }

    /**
     * Builds and returns the compiled JSON Schema array structure.
     *
     * @return array<string, mixed>
     */
    public function build(): array
    {
        return [
            'type' => 'object',
            'properties' => $this->properties,
            'required' => $this->required,
            'additionalProperties' => $this->additionalProperties,
        ];
    }
}
