<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionProperty;

class FieldValidator
{
    /** @var array */
    private static $typeMapping = [
        'int' => 'integer',
        'bool' => 'boolean',
        'float' => 'double',
    ];

    /** @var bool */
    private $hasTypeDeclaration = false;

    /** @var bool */
    public $isNullable = false;

    /** @var bool */
    public $isMixed = false;

    /** @var bool */
    public $isMixedArray = false;

    /** @var bool */
    public $hasDefaultValue = false;

    /** @var array */
    public $allowedTypes = [];

    /** @var array */
    public $allowedArrayTypes = [];


    public static function fromReflection(ReflectionProperty $property): FieldValidator
    {
        return new self(
            $property->getDocComment() ?: null,
            $property->isDefault()
        );
    }

    public function __construct(?string $docComment = null, bool $hasDefaultValue = false)
    {
        preg_match(
            '/@var ((?:(?:[\w?|\\\\<>])+(?:\[])?)+)/',
            $docComment ?? '',
            $matches
        );

        $definition = $matches[1] ?? '';

        $this->hasTypeDeclaration = $definition !== '';
        $this->hasDefaultValue = $hasDefaultValue;
        $this->isNullable = $this->resolveNullable($definition);
        $this->isMixed = $this->resolveIsMixed($definition);
        $this->isMixedArray = $this->resolveIsMixedArray($definition);
        $this->allowedTypes = $this->resolveAllowedTypes($definition);
        $this->allowedArrayTypes = $this->resolveAllowedArrayTypes($definition);
    }

    public function isValidType($value): bool
    {
        if (! $this->hasTypeDeclaration) {
            return true;
        }

        if ($this->isMixed) {
            return true;
        }

        if (is_iterable($value) && $this->isMixedArray) {
            return true;
        }

        if ($this->isNullable && $value === null) {
            return true;
        }

        if (is_iterable($value)) {
            foreach ($this->allowedArrayTypes as $type) {
                $isValid = $this->assertValidArrayTypes($type, $value);

                if ($isValid) {
                    return true;
                }
            }
        }

        foreach ($this->allowedTypes as $type) {
            $isValidType = $this->assertValidType($type, $value);

            if ($isValidType) {
                return true;
            }
        }

        return false;
    }

    private function assertValidType(string $type, $value): bool
    {
        return $value instanceof $type || gettype($value) === $type;
    }

    private function assertValidArrayTypes(string $type, $collection): bool
    {
        foreach ($collection as $value) {
            if (! $this->assertValidType($type, $value)) {
                return false;
            }
        }

        return true;
    }

    private function resolveNullable(string $definition): bool
    {
        if (! $definition) {
            return true;
        }

        if (Str::contains($definition, ['mixed', 'null', '?'])) {
            return true;
        }

        return false;
    }

    private function resolveIsMixed(string $definition): bool
    {
        return Str::contains($definition, ['mixed']);
    }

    private function resolveIsMixedArray(string $definition): bool
    {
        $types = $this->normaliseTypes(...explode('|', $definition));

        foreach ($types as $type) {
            if (in_array($type, ['iterable', 'array'])) {
                return true;
            }
        }

        return false;
    }

    private function resolveAllowedTypes(string $definition): array
    {
        return $this->normaliseTypes(...explode('|', $definition));
    }

    private function resolveAllowedArrayTypes(string $definition): array
    {
        return $this->normaliseTypes(...array_map(
            function (string $type) {
                if (! $type) {
                    return;
                }

                if (strpos($type, '[]') !== false) {
                    return str_replace('[]', '', $type);
                }

                if (strpos($type, 'iterable<') !== false) {
                    return str_replace(['iterable<', '>'], ['', ''], $type);
                }

                return null;
            },
            explode('|', $definition)
        ));
    }

    private function normaliseTypes(?string ...$types): array
    {
        return array_filter(array_map(
            function (?string $type) {
                return self::$typeMapping[$type] ?? $type;
            },
            $types
        ));
    }
}
