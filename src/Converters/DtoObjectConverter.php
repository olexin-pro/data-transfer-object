<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject\Converters;

use Ol3x1n\DataTransferObject\AbstractDTO;
use Ol3x1n\DataTransferObject\Contracts\TypeConverterInterface;

final readonly class DtoObjectConverter implements TypeConverterInterface
{
    public function __construct(
        private string $dtoClass
    ) {}

    public function convert(mixed $value): ?AbstractDTO
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof AbstractDTO) {
            return $value;
        }

        if (is_array($value)) {
            return new $this->dtoClass($value);
        }
        throw new \TypeError(
            sprintf(
                "Cannot convert value of type '%s' to DTO '%s'",
                get_debug_type($value),
                $this->dtoClass
            )
        );
    }
}
