<?php

declare(strict_types=1);

namespace DataTransferObject\Tests\Unit;

use DataTransferObject\Tests\TestCase;
use DataTransferObject\Tests\Examples\ExampleDTO;
use DataTransferObject\Tests\Examples\NestedDTO;

final class DTOTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function test_basic_dto_creation()
    {
        $dto = new ExampleDTO([
            'userId' => '5',
            'name' => 'Alex',
            'data' => '{"a":1}',
            'nested' => [
                'code' => '7',
                'label' => 'VIP'
            ]
        ]);

        $this->assertSame(5, $dto->userId);
        $this->assertSame('Alex', $dto->name);
        $this->assertSame(['a' => 1], $dto->data);

        $this->assertInstanceOf(NestedDTO::class, $dto->nested);
        $this->assertSame(7, $dto->nested->code);
        $this->assertSame('VIP', $dto->nested->label);
    }

    public function test_required_field_validation()
    {
        $this->expectException(\Ol3x1n\DataTransferObject\Exceptions\MissingRequiredField::class);

        new ExampleDTO([
            'name' => 'Alex'
        ]);
    }

    public function test_raw_data()
    {
        $input = [
            'userId' => '10',
            'name' => 'Original',
            'data' => []
        ];

        $dto = new ExampleDTO($input);

        $this->assertSame($input, $dto->getRawData());
    }

    public function test_to_array_returns_converted_data()
    {
        $dto = new ExampleDTO([
            'userId' => '5',
            'name' => 'Alex',
            'data' => '{"a":2}',
            'nested' => [
                'code' => '7',
                'label' => 'VIP'
            ]
        ]);

        $arr = $dto->toArray();

        $this->assertSame(5, $arr['userId']);
        $this->assertSame(['a' => 2], $arr['data']);
        $this->assertIsArray($arr['nested']);
        $this->assertSame(7, $arr['nested']['code']);
    }

    /**
     * @throws \ReflectionException
     */
    public function test_nested_dto_serialization()
    {
        $dto = new ExampleDTO([
            'userId' => 1,
            'name' => null,
            'data' => [],
            'nested' => [
                'code' => 100,
                'label' => 'Level 1'
            ]
        ]);

        $result = $dto->toArray();

        $this->assertSame([
            'userId' => 1,
            'name' => null,
            'data' => [],
            'nested' => [
                'code' => 100,
                'label' => 'Level 1'
            ]
        ], $result);
    }
}
