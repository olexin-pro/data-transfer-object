<?php

declare(strict_types=1);

namespace DataTransferObject\Tests\Unit;

use DataTransferObject\Tests\Examples\ExampleDTO;
use DataTransferObject\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Ol3x1n\DataTransferObject\Laravel\DTOCast;

final class CastTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function test_dto_cast()
    {
        $model = new class extends Model {
            protected $table = 'test';
            protected $casts = [
                'profile' => DTOCast::class . ':' . ExampleDTO::class,
            ];
        };

        $model->profile = new ExampleDTO([
            'userId' => '55',
            'name' => 'Alex',
            'data' => [],
        ]);

        $arr = $model->getAttributes();
        $this->assertJson($arr['profile']);

        $model->syncOriginal();
        $loaded = $model->profile;

        $this->assertInstanceOf(ExampleDTO::class, $loaded);
        $this->assertSame(55, $loaded->userId);
    }
}
