<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Http\Request;
use JsonSerializable;
use Ol3x1n\DataTransferObject\Contracts\DTOInterface;
use Ol3x1n\DataTransferObject\Converters\DtoObjectConverter;
use Ol3x1n\DataTransferObject\Enums\TypeEnum;
use Ol3x1n\DataTransferObject\Exceptions\MissingRequiredField;
use Ol3x1n\DataTransferObject\Laravel\DTOCast;
use Ol3x1n\DataTransferObject\Services\ConverterResolver;
use Ol3x1n\DataTransferObject\Services\ReflectionStorage;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

abstract class AbstractDTO implements DTOInterface, JsonSerializable, Castable
{
    protected readonly array $_raw;
    protected array $_normalized;
    private ConverterResolver $resolver;

    /**
     * @throws ReflectionException
     */
    public function __construct(array $data)
    {
        $this->resolver = new ConverterResolver();
        $this->_raw = $data;
        $this->_normalized = $this->normalizeArrayKeys($data);
        $this->initializeFields();
    }

    public function getRawData(): array
    {
        return $this->_raw;
    }

    public function getNormalized(): array
    {
        return $this->_normalized;
    }

    /**
     * @throws ReflectionException
     */
    public function toArray(): array
    {
        $out = [];

        foreach ($this->getReflection()->getProperties() as $property) {
            if ($this->isInternalProperty($property)) {
                continue;
            }

            $name = $property->getName();
            $value = $property->getValue($this);

            $out[$name] = $value instanceof DTOInterface
                ? $value->toArray()
                : $value;
        }

        return $out;
    }

    private function isInternalProperty(ReflectionProperty $property): bool
    {
        return $property->getName() === '_raw'
            || $property->getName() === '_normalized';
    }

    /**
     * @throws ReflectionException
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }


    /**
     * @throws ReflectionException
     */
    private function initializeFields(): void
    {
        foreach ($this->getReflection()->getProperties() as $property) {
            $attributes = $property->getAttributes(Field::class);

            foreach ($attributes as $attribute) {
                /** @var Field $field */
                $field = $attribute->newInstance();
                $this->applyFieldAttribute($property, $field);
            }
        }
    }

    private function applyFieldAttribute(ReflectionProperty $property, Field $meta): void
    {
        $key = $this->normalizeKey($meta->fieldName);

        if ($meta->required && !array_key_exists($key, $this->_normalized)) {
            throw new MissingRequiredField(
                "Missing required field '{$meta->fieldName}' for property {$property->getName()}"
            );
        }

        $rawValue = $this->_normalized[$key] ?? null;

        if ($meta->type === TypeEnum::DTO) {
            $type = $property->getType();

            if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
                throw new \LogicException(
                    "Field '{$property->getName()}' with TypeEnum::DTO must have a class type"
                );
            }

            $dtoClass = $type->getName();
            $converter = new DtoObjectConverter($dtoClass);
        } else {
            $converter = $this->resolver->resolve($meta->type);
        }
        $value = $converter->convert($rawValue);

        $this->validatePropertyType($property, $value);

        $property->setValue($this, $value);
    }

    private function validatePropertyType(ReflectionProperty $property, mixed $value): void
    {
        $type = $property->getType();

        if ($type === null) {
            return;
        }


        if ($value === null) {
            if ($type->allowsNull()) {
                return;
            }

            throw new \TypeError(
                "Property {$property->getName()} does not allow null"
            );
        }

        $expected = $type->getName();
        $actual = is_object($value) ? $value::class : get_debug_type($value);

        if ($actual !== $expected) {
            throw new \TypeError(
                "Property {$property->getName()} expects {$expected}, {$actual} given"
            );
        }
    }

    private function normalizeArrayKeys(array $input): array
    {
        $out = [];

        foreach ($input as $key => $value) {
            $key = $this->normalizeKey($key);
            $out[$key] = ($value === '' ? null : $value);
        }

        return $out;
    }

    private function normalizeKey(string $key): string
    {
        $key = preg_replace('/([a-z])([A-Z])/', '$1_$2', $key);
        return strtolower($key);
    }

    /**
     * @throws ReflectionException
     */
    private function getReflection(): ReflectionClass
    {
        return ReflectionStorage::get(static::class);
    }

    public static function castUsing(array $arguments): DTOCast
    {
        return new DTOCast(static::class);
    }

    /**
     * @throws ReflectionException
     */
    public static function fromRequest(Request $request): static
    {
        return new static(
            array_merge(
                $request->query(),
                $request->post(),
                $request->json()->all(),
                $request->files->all()
            )
        );
    }
}
