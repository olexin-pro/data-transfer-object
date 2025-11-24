<?php

declare(strict_types=1);

namespace DataTransferObject\Tests\Unit;

use DataTransferObject\Tests\TestCase;
use JsonException;
use Ol3x1n\DataTransferObject\Converters\ArrayConverter;

final class ConverterTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function test_array_converter()
    {
        $conv = new ArrayConverter();

        $this->assertSame(['a' => 1], $conv->convert('{"a":1}'));
        $this->assertSame([1,2], $conv->convert([1,2]));
    }
}
