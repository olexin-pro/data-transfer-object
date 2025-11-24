<?php

declare(strict_types=1);

namespace DataTransferObject\Tests\Examples;

use Ol3x1n\DataTransferObject\AbstractDTO;
use Ol3x1n\DataTransferObject\Enums\TypeEnum;
use Ol3x1n\DataTransferObject\Field;

final class ExampleDTO extends AbstractDTO
{
    #[Field('user_id', TypeEnum::INT, required: true)]
    public int $userId;

    #[Field('name', TypeEnum::STRING)]
    public ?string $name;

    #[Field('data', TypeEnum::ARRAY)]
    public array $data;

    #[Field('nested', TypeEnum::DTO)]
    public ?NestedDTO $nested;

}
