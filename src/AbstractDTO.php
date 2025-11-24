<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject;

use Illuminate\Http\Request;
use JsonSerializable;
use Ol3x1n\DataTransferObject\Contracts\DTOInterface;
use Ol3x1n\DataTransferObject\Exceptions\MissingRequiredField;
use Ol3x1n\DataTransferObject\Laravel\DTOCast;
use Ol3x1n\DataTransferObject\Services\ConverterResolver;
use Ol3x1n\DataTransferObject\Services\ReflectionStorage;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

abstract class AbstractDTO implements DTOInterface, JsonSerializable
{
    protected readonly array $data;
    protected array $normalizedData;
    private ConverterResolver $resolver;

    /**
     * @throws ReflectionException
     */
    public function __construct(array $data)
    {
        $this->resolver = new ConverterResolver();
        $this->data = $data;
        $this->normalizedData = $this->normalizeArrayKeys($data);
        $this->initializeFields();
    }

    public function getRawData(): array
    {
        return $this->data;
    }

    public function getNormalizedData(): array
    {
        return $this->normalizedData;
    }

    /**
     * @throws ReflectionException
     */
    public function toArray(): array
    {
        $out = [];

        foreach ($this->getReflection()->getProperties() as $property) {

            $name = $property->getName();
            $value = $property->getValue($this);

            $out[$name] = $value instanceof DTOInterface
                ? $value->toArray()
                : $value;
        }

        return $out;
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

        if ($meta->required && !array_key_exists($key, $this->normalizedData)) {
            throw new MissingRequiredField(
                "Missing required field '{$meta->fieldName}' for property {$property->getName()}"
            );
        }

        $rawValue = $this->normalizedData[$key] ?? null;
        $converter = $this->resolver->resolve($meta->type);
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

        $expected = $type->getName();

        if ($value === null && $type->allowsNull()) {
            return;
        }

        $actual = is_object($value) ? $value::class : gettype($value);

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
            $out[$key] = $value === '' ? null : $value;
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

    public static function cast(): string
    {
        return DTOCast::class . ':' . static::class;
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
