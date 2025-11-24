<?php

declare(strict_types=1);

namespace DataTransferObject\Tests\Examples;

use Ol3x1n\DataTransferObject\AbstractDTO;
use Ol3x1n\DataTransferObject\Enums\TypeEnum;
use Ol3x1n\DataTransferObject\Field;

final class NestedDTO extends AbstractDTO
{
    #[Field('code', TypeEnum::INT)]
    public int $code;

    #[Field('label', TypeEnum::STRING)]
    public string $label;
}
