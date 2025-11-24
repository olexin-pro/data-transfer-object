<?php

declare(strict_types=1);

namespace DataTransferObject\Tests\Unit;

use DataTransferObject\Tests\Examples\ExampleDTO;
use DataTransferObject\Tests\TestCase;
use Illuminate\Http\Request;

final class FromRequestTest extends TestCase
{
    public function test_dto_from_request()
    {
        $request = Request::create('/test', 'POST', [
            'userId' => '5',
            'name' => 'John'
        ]);

        $dto = ExampleDTO::fromRequest($request);

        $this->assertSame(5, $dto->userId);
        $this->assertSame('John', $dto->name);
    }
}
