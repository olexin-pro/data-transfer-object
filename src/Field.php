<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject;
use Attribute;
use Ol3x1n\DataTransferObject\Enums\TypeEnum;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Field
{

    /**
     * @param string $fieldName
     * @param TypeEnum|class-string<> $type
     * @param bool $required
     */
    public function __construct(
        public string $fieldName,
        public TypeEnum|string $type = TypeEnum::DYNAMIC,
        public bool $required = false
    ) {}
}